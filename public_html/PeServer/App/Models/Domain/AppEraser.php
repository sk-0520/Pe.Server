<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;
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
		ILoggerFactory $loggerFactory
	) {
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	public function execute(): void
    {
		$this->logger->info('未実装', $this->config);
	}

	#endregion
}
