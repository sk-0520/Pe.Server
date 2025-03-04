<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Log\ILoggerFactory;

class AppMigrationManager
{
	public function __construct(
		private AppConfiguration $appConfig,
		private ILoggerFactory $loggerFactory
	) {
		//NOP
	}
}
