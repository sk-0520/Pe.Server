<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;

class AppEraser
{
	public function __construct(
		private AppConfiguration $config,
		private ILogger $logger
	) {
	}

	#region function

	public function execute() : void {
		$this->logger->info('未実装');
	}

	#endregion
}
