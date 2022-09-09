<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup;

use PeServer\App\Models\Setup\Versions\SetupVersion_0000;
use PeServer\App\Models\Setup\Versions\SetupVersion_0001;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\File;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Text;

class SetupLoader
{
	#region variable

	private array $versions = [
		SetupVersion_0000::class,
		SetupVersion_0001::class,
	];

	private ILogger $logger;

	#endregion

	public function __construct(
		private IDatabaseConnection $defaultConnection,
		private ILoggerFactory $loggerFactory
	) {
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	public function execute(): void
	{
		$dbVersion = -1;
		// SQLite を使うのは決定事項である！
		$connectionSetting = $this->defaultConnection->getConnectionSetting();
		$filePath = Text::replace($connectionSetting->dsn, 'sqlite:', Text::EMPTY);

		if (File::exists($filePath)) {
			$this->logger->info('DBあり: {0}', $filePath);

			$context = $this->defaultConnection->open();
			$checkCount = $context->selectSingleCount("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
			if (0 < $checkCount) {
				$row = $context->queryFirstOrNull("select version from database_version");
				if ($row !== null) {
					$dbVersion = (int)$row->fields['version'];;
				}
			}
		}

		$this->logger->info('DBバージョン: {0}', $dbVersion);
	}

	#endregion
}
