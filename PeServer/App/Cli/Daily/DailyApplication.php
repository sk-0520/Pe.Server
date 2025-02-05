<?php

declare(strict_types=1);

namespace PeServer\App\Cli\Daily;

use Error;
use PeServer\App\Cli\AppApplicationBase;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\Core\Environment;
use PeServer\Core\Log\ILoggerFactory;

class DailyApplication extends AppApplicationBase
{
	//public function __construct(public DailyParameter $parameter, private Environment $environment, ILoggerFactory $loggerFactory)
	public function __construct(
		public DailyParameter $parameter,
		private AppArchiver $appArchiver,
		ILoggerFactory $loggerFactory
	) {
		parent::__construct($loggerFactory);
	}

	#region function

	private function backup(): void
	{
		$size = $this->appArchiver->backup();
		$this->appArchiver->rotate();

		$this->logger->info("size: {0}", $size);

		// 日曜だけバックアップ送信でいいわ
		$week = (int)$this->beginTimestamp->format('w');
		if ($week === 0) {
			$this->appArchiver->sendLatestArchive("backup", true);
		}
	}

	private function deleteOldData(): void {}

	private function vacuumAccessLog(): void {}

	private function rebuild(): void {}

	#endregion

	#region AppApplicationBase

	public function executeImpl(): void
	{
		$this->logger->info("Backup");
		$this->backup();

		$this->logger->info("Delete Old Data");
		$this->deleteOldData();

		$this->logger->info("Vacuum Access Log");
		$this->vacuumAccessLog();

		$this->logger->info("Rebuild");
		$this->rebuild();
	}

	#endregion
}
