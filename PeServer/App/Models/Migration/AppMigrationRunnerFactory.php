<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\Log\ILoggerFactory;

class AppMigrationRunnerFactory extends DiFactoryBase
{
	#region function

	public function create(): AppMigrationRunner
	{
		return $this->container->new(AppMigrationRunner::class);
	}

	#endregion
}


// private AppConfiguration $appConfig,
// private ILoggerFactory $loggerFactory
