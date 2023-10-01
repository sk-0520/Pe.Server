<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;

/**
 * 所謂削除バッチ。
 *
 * ふるくなったなんやかんやを削除する予定。
 */
class AppEraser
{
	#region variable

	private ILogger $logger;

	#endregion

	public function __construct(
		private AppConfiguration $config,
		private AppDatabaseConnection $databaseConnection,
		ILoggerFactory $loggerFactory
	) {
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	public function execute(): void
    {
		$database = $this->databaseConnection->open();

		$this->logger->info('未実装', $this->config);

		$this->logger->debug('キュッとする処理だけ対応');
		$database->execute('vacuum');
		$database->execute('reindex');
		$database->execute('analyze');
	}

	#endregion
}
