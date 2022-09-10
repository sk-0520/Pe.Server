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

class SetupRunner
{
	#region variable

	/**
	 * @phpstan-var class-string<SetupVersionBase>[]
	 */
	private array $versions;

	private ILogger $logger;

	#endregion

	public function __construct(
		private IDatabaseConnection $defaultConnection,
		private ILoggerFactory $loggerFactory
	) {
		$this->logger = $loggerFactory->createLogger($this);

		/** @var class-string<SetupVersionBase>[] */
		$versions = [
			SetupVersion_0000::class,
			SetupVersion_0001::class,
		];
		// 定義ミス対応としてバージョン間並べ替え（ミスるな）
		usort($versions, fn ($a, $b) => SetupVersionBase::getVersion($a) <=> SetupVersionBase::getVersion($b));
		$this->versions = $versions;
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

			foreach ($this->versions as $version) {
				$ver = SetupVersionBase::getVersion($version);
				if ($ver <= $dbVersion) {
					continue;
				}

				$this->logger->info('VERSION: {0}', $version);

				/** @var SetupVersionBase */
				$setupVersion = new $version($this->loggerFactory);
				$setupVersion->migrate($ioArg, $dbArg);
				$newVersion = $ver;
			}

			if ($dbVersion <= $newVersion) {
				$setupLastVersion = new SetupVersionLast($dbVersion, $newVersion, $this->loggerFactory);
				$setupLastVersion->migrate($ioArg, $dbArg);
			}

			return true;
		});

		$context->execute('PRAGMA foreign_keys = ON;');
	}

	#endregion
}
