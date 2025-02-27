<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Setup\Versions\Session\SessionSetupVersionBase;
use PeServer\App\Models\Setup\Versions\Session\SessionSetupVersion_0000;
use PeServer\App\Models\Setup\Versions\Session\SessionSetupVersionLast;
use PeServer\App\Models\Setup\Versions\SetupVersion_0000;
use PeServer\App\Models\Setup\Versions\SetupVersion_0001;
use PeServer\App\Models\Setup\Versions\SetupVersion_0002;
use PeServer\App\Models\Setup\Versions\SetupVersion_0003;
use PeServer\App\Models\Setup\Versions\SetupVersion_0004;
use PeServer\App\Models\Setup\Versions\SetupVersion_0005;
use PeServer\App\Models\Setup\Versions\SetupVersion_0006;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\SetupVersionLast;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Database\DatabaseUtility;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Store\SessionHandler\SqliteSessionHandler;
use PeServer\Core\Text;

class SetupRunner
{
	#region variable

	/**
	 * @var class-string<SetupVersionBase>[]
	 */
	private array $versions;

	/**
	 * @var class-string<SessionSetupVersionBase>[]
	 */
	private array $sessionVersions;

	private ILogger $logger;

	#endregion

	public function __construct(
		private IDatabaseConnection $defaultConnection,
		private AppConfiguration $appConfig,
		private ILoggerFactory $loggerFactory
	) {
		$this->logger = $loggerFactory->createLogger($this);

		/** @var class-string<SetupVersionBase>[] */
		$versions = [
			SetupVersion_0000::class,
			SetupVersion_0001::class,
			SetupVersion_0002::class,
			SetupVersion_0003::class,
			SetupVersion_0004::class,
			SetupVersion_0005::class,
			SetupVersion_0006::class,
		];
		// 定義ミス対応としてバージョン間並べ替え（ミスるな）
		$this->versions = Arr::sortCallbackByValue($versions, fn($a, $b) => SetupVersionBase::getVersion($a) <=> SetupVersionBase::getVersion($b));

		/** @var class-string<SessionSetupVersionBase>[] */
		$sessionVersions = [
			SessionSetupVersion_0000::class,
		];
		$this->sessionVersions = Arr::sortCallbackByValue($sessionVersions, fn($a, $b) => SetupVersionBase::getVersion($a) <=> SetupVersionBase::getVersion($b));
	}

	#region function

	/**
	 * Undocumented function
	 *
	 * @param IDatabaseConnection $connection
	 * @param class-string<SetupVersionBase>[] $versions
	 */
	private function executeCore(string $mode, IDatabaseConnection $connection, array $versions, string $lastVersion): void
	{
		$dbVersion = -1;
		// SQLite を使うのは決定事項である！
		$connectionSetting = $connection->getConnectionSetting();

		if (!DatabaseUtility::isSqliteMemoryMode($connectionSetting)) {
			$filePath = DatabaseUtility::getSqliteFilePath($connectionSetting);

			if (File::exists($filePath)) {
				$this->logger->info('<{0}> DBあり: {1}', $mode, $filePath);

				$context = $connection->open();
				$checkCount = $context->selectSingleCount("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
				if (0 < $checkCount) {
					$row = $context->queryFirstOrNull("select version from database_version");
					if ($row !== null) {
						$dbVersion = (int)$row->fields['version'];
						;
					}
				}
			}
		}

		$this->logger->info('<{0}> DBバージョン: {1}', $mode, $dbVersion);

		$newVersion = 0;
		$context = $connection->open();
		$context->execute('PRAGMA foreign_keys = OFF;');

		$context->transaction(function (IDatabaseContext $context) use ($dbVersion, &$newVersion, $mode, $versions, $lastVersion) {
			// ええねん、SQLite しか使わん
			$ioArg = new IOSetupArgument();
			$dbArg = new DatabaseSetupArgument($context);

			foreach ($versions as $version) {
				$ver = SetupVersionBase::getVersion($version);
				if ($ver <= $dbVersion) {
					$this->logger->info('<{0}> 無視バージョン: {1}', $mode, $ver);
					continue;
				}

				$this->logger->info('<{0}> VERSION: {1}', $mode, $ver);

				$setupVersion = new $version($this->appConfig, $this->loggerFactory);
				$setupVersion->migrate($ioArg, $dbArg);
				$newVersion = $ver;
			}

			if ($dbVersion <= $newVersion) {
				/** @var SetupVersionBase */
				$setupLastVersion = new $lastVersion($dbVersion, $newVersion, $this->appConfig, $this->loggerFactory);
				$setupLastVersion->migrate($ioArg, $dbArg);
				$this->logger->info('<{0}> DBバージョン更新: {1}', $mode, $newVersion);
				return true;
			}

			$this->logger->info('<{0}> DBバージョン未更新: {1}', $mode, $dbVersion);
			return false;
		});

		$context->execute('PRAGMA foreign_keys = ON;');
	}

	public function execute(): void
	{
		$this->executeCore("DB:DEFAULT", $this->defaultConnection, $this->versions, SetupVersionLast::class);

		if ($this->appConfig->setting->store->session->handler === 'sqlite') {
			$connection = SqliteSessionHandler::createConnection($this->appConfig->setting->store->session->save, null, $this->loggerFactory);
			$this->executeCore("DB:SESSION", $connection, $this->sessionVersions, SessionSetupVersionLast::class);
		}
	}

	#endregion
}
