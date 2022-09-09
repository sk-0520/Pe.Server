<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup;

use PeServer\App\Models\Setup\Versions\SetupVersion_0000;
use PeServer\App\Models\Setup\Versions\SetupVersion_0001;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\SetupVersionLast;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\File;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Text;

class SetupLoader
{
	#region variable

	/**
	 * @phpstan-var class-string<SetupVersionBase>[]
	 */
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

		$newVersion = 0;
		$context = $this->defaultConnection->open();
		$context->execute('PRAGMA foreign_keys = OFF;');

		$context->transaction(function (IDatabaseContext $context) use ($dbVersion, &$newVersion) { // ええねん、SQLite しか使わん
			$ioArg = new IOSetupArgument();
			$dbArg = new DatabaseSetupArgument($context);

			for ($i = $dbVersion + 1; $i < Arr::getCount($this->versions); $i++) {
				$setupVersionClassName = $this->versions[$i];
				$this->logger->info('CLASS: {0}', $setupVersionClassName);

				/** @var SetupVersionBase */
				$setupVersion = new $setupVersionClassName($this->loggerFactory);
				$setupVersion->migrate($ioArg, $dbArg);

				$newVersion = $i;
			}

			if($dbVersion <= $newVersion) {
				$setupLastVersion = new SetupVersionLast($dbVersion, $newVersion, $this->loggerFactory);
				$setupLastVersion->migrate($ioArg, $dbArg);
			}

			return true;
		});

		$context->execute('PRAGMA foreign_keys = ON;');
	}

	#endregion
}
