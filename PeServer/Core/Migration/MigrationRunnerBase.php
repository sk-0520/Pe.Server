<?php

declare(strict_types=1);

namespace PeServer\Core\Migration;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Throws\ArgumentException;

abstract class MigrationRunnerBase
{
	#region variable

	protected ILogger $logger;

	#endregion

	public function __construct(protected ILoggerFactory $loggerFactory)
	{
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	abstract protected function getCurrentVersion(string $mode, IDatabaseConnection $connection): int;

	abstract protected function beforeMigrate(string $mode, IDatabaseContext $context): void;
	abstract protected function afterMigrate(string $mode, IDatabaseContext $context): void;

	/**
	 * Undocumented function
	 *
	 * @param IDatabaseConnection $connection
	 * @param class-string<MigrationBase>[] $migrationClasses
	 * @param class-string<MigrationBase> $lastMigrationClass
	 */
	protected function executeCore(string $mode, IDatabaseConnection $connection, array $migrationClasses, string $lastMigrationClass): void
	{
		$sortedMigrationClasses = Arr::toUnique(Arr::sortCallbackByValue($migrationClasses, fn($a, $b) => MigrationVersion::getVersion($a) <=> MigrationVersion::getVersion($b)));
		$length = count($migrationClasses);
		$sortedLength = count($sortedMigrationClasses);
		if ($length !== $sortedLength) {
				throw new ArgumentException("length: {$length} !== {$sortedLength}(sort/unique)");
		}
		for ($i = 0; $i < $length; $i++) {
			$migrationClass = $migrationClasses[$i];
			$sortedMigrationClass = $sortedMigrationClasses[$i];
			if ($migrationClass !== $sortedMigrationClass) {
				throw new ArgumentException("index: {$i}");
			}
		}

		$version = $this->getCurrentVersion($mode, $connection);

		$this->logger->info('<{0}> マイグレーションバージョン: {1}', $mode, $version);

		$newVersion = 0;
		$context = $connection->open();

		$this->beforeMigrate($mode, $context);

		$context->transaction(function (IDatabaseContext $context) use ($version, &$newVersion, $mode, $migrationClasses, $lastMigrationClass) {
			$argument = new MigrationArgument($context);

			foreach ($migrationClasses as $migrationClass) {
				$migrationVersion = MigrationVersion::getVersion($migrationClass);
				if ($migrationVersion <= $version) {
					$this->logger->info('<{0}> 無視バージョン: {1}', $mode, $migrationVersion);
					continue;
				}

				$this->logger->info('<{0}> VERSION: {1}', $mode, $migrationVersion);

				/** @var MigrationBase */
				$migration = new $migrationClass($migrationVersion, $this->loggerFactory);
				$migration->migrate($argument);
				$newVersion = $migrationVersion;
			}

			if ($version < $newVersion) {
				/** @var MigrationBase */
				$setupLastVersion = new $lastMigrationClass($newVersion, $this->loggerFactory);
				$setupLastVersion->migrate($argument);
				$this->logger->info('<{0}> マイグレーションバージョン更新: {1}', $mode, $newVersion);
				return true;
			}

			$this->logger->info('<{0}> マイグレーションバージョン未更新: {1}', $mode, $version);
			return false;
		});

		$this->afterMigrate($mode, $context);
	}

	abstract public function execute(): void;

	#endregion
}
