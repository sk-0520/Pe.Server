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

	protected abstract function getCurrentVersion(string $mode, IDatabaseConnection $connection): int;

	protected abstract function beforeMigrate(string $mode, IDatabaseContext $context): void;
	protected abstract function afterMigrate(string $mode, IDatabaseContext $context): void;

	/**
	 * Undocumented function
	 *
	 * @param IDatabaseConnection $connection
	 * @param class-string<MigrationBase>[] $migrationClasses
	 * @param class-string<MigrationBase> $lastMigrationClass
	 */
	protected function executeCore(string $mode, IDatabaseConnection $connection, array $migrationClasses, string $lastMigrationClass): void
	{
		$sortedMigrationClasses = Arr::sortCallbackByValue($migrationClasses, fn($a, $b) => MigrationVersion::getVersion($a) <=> MigrationVersion::getVersion($b));
		$length = count($migrationClasses);
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

	// /**
	//  * Undocumented function
	//  *
	//  * @param IDatabaseConnection $connection
	//  * @param class-string<MigrationBase>[] $versions
	//  * @param class-string<MigrationBase> $lastVersion
	//  */
	// private function executeCore(string $mode, IDatabaseConnection $connection, array $versions, string $lastVersion): void
	// {
	// 	$dbVersion = -1;
	// 	// SQLite を使うのは決定事項である！
	// 	$connectionSetting = $connection->getConnectionSetting();

	// 	if (!DatabaseUtility::isSqliteMemoryMode($connectionSetting)) {
	// 		$filePath = DatabaseUtility::getSqliteFilePath($connectionSetting);

	// 		if (File::exists($filePath)) {
	// 			$this->logger->info('<{0}> DBあり: {1}', $mode, $filePath);

	// 			$context = $connection->open();
	// 			$checkCount = $context->selectSingleCount("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
	// 			if (0 < $checkCount) {
	// 				$row = $context->queryFirstOrNull("select version from database_version");
	// 				if ($row !== null) {
	// 					$dbVersion = (int)$row->fields['version'];;
	// 				}
	// 			}
	// 		}
	// 	}

	// 	$this->logger->info('<{0}> DBバージョン: {1}', $mode, $dbVersion);

	// 	$newVersion = 0;
	// 	$context = $connection->open();
	// 	$context->execute('PRAGMA foreign_keys = OFF;');

	// 	$context->transaction(function (IDatabaseContext $context) use ($dbVersion, &$newVersion, $mode, $versions, $lastVersion) {
	// 		// ええねん、SQLite しか使わん
	// 		$ioArg = new IOSetupArgument();
	// 		$dbArg = new DatabaseSetupArgument($context);

	// 		foreach ($versions as $version) {
	// 			$ver = SetupVersionBase::getVersion($version);
	// 			if ($ver <= $dbVersion) {
	// 				$this->logger->info('<{0}> 無視バージョン: {1}', $mode, $ver);
	// 				continue;
	// 			}

	// 			$this->logger->info('<{0}> VERSION: {1}', $mode, $ver);

	// 			$setupVersion = new $version($this->appConfig, $this->loggerFactory);
	// 			$setupVersion->migrate($ioArg, $dbArg);
	// 			$newVersion = $ver;
	// 		}

	// 		if ($dbVersion <= $newVersion) {
	// 			/** @var SetupVersionBase */
	// 			$setupLastVersion = new $lastVersion($dbVersion, $newVersion, $this->appConfig, $this->loggerFactory);
	// 			$setupLastVersion->migrate($ioArg, $dbArg);
	// 			$this->logger->info('<{0}> DBバージョン更新: {1}', $mode, $newVersion);
	// 			return true;
	// 		}

	// 		$this->logger->info('<{0}> DBバージョン未更新: {1}', $mode, $dbVersion);
	// 		return false;
	// 	});

	// 	$context->execute('PRAGMA foreign_keys = ON;');
	// }

	// public function execute(): void
	// {
	// 	$this->executeCore("DB:DEFAULT", $this->defaultConnection, $this->versions, SetupVersionLast::class);

	// 	if (SessionHandlerFactoryUtility::isFactory($this->appConfig->setting->store->session->handlerFactory)) {
	// 		$connection = SqliteSessionHandler::createConnection($this->appConfig->setting->store->session->save, null, $this->loggerFactory);
	// 		$this->executeCore("DB:SESSION", $connection, $this->sessionVersions, SessionSetupVersionLast::class);
	// 	}
	// }

	public abstract function execute(): void;

	#endregion
}
