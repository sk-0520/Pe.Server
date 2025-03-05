<?php

declare(strict_types=1);

namespace PeServer\Core\Migration;

use PeServer\Core\Database\DatabaseUtility;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\File;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;

abstract class SqliteMigrationRunnerBase extends MigrationRunnerBase
{
	#region function

	protected abstract function getCurrentVersionCore(string $mode, IDatabaseContext $context): int;

	#endregion

	#region MigrationRunnerBase

	protected function getCurrentVersion(string $mode, IDatabaseConnection $connection): int
	{
		$version = MigrationVersion::INITIAL_VERSION;

		// SQLite を使うのは決定事項である！
		$connectionSetting = $connection->getConnectionSetting();

		if (!DatabaseUtility::isSqliteMemoryMode($connectionSetting)) {
			$filePath = DatabaseUtility::getSqliteFilePath($connectionSetting);

			if (File::exists($filePath)) {
				$this->logger->info('<{0}> DBあり: {1}', $mode, $filePath);

				$context = $connection->open();

				$version = $this->getCurrentVersionCore($mode, $context);
			}
		}

		return $version;
	}

	protected function beforeMigrate(string $mode, IDatabaseContext $context): void
	{
		$context->execute('PRAGMA foreign_keys = OFF;');
	}

	protected function afterMigrate(string $mode, IDatabaseContext $context): void
	{
		$context->execute('PRAGMA foreign_keys = ON;');
	}

	#endregion
}
