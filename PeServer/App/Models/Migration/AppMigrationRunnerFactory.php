<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\SessionHandler\SqliteSessionHandler;
use PeServer\App\Models\SessionHandler\SqliteSessionHandlerFactory;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Store\SessionHandlerFactoryUtility;
use PeServer\Core\Throws\InvalidOperationException;

class AppMigrationRunnerFactory extends DiFactoryBase
{
	use DiFactoryTrait;

	#region function

	public function create(): AppMigrationRunner
	{
		$defaultConnection = $this->container->new(AppDatabaseConnection::class);
		$loggerFactory = $this->container->new(ILoggerFactory::class);
		$sessionConnection = null;
		/** @var AppConfiguration */
		$appConfig = $this->container->new(AppConfiguration::class);
		if (SessionHandlerFactoryUtility::isFactory($appConfig->setting->store->session->handlerFactory)) {
			if (SqliteSessionHandlerFactory::isSqliteFactory($appConfig->setting->store->session->handlerFactory)) {
				$sessionConnection = SqliteSessionHandler::createConnection($appConfig->setting->store->session->save, null, $loggerFactory);
			}
		}

		return new AppMigrationRunner($defaultConnection, $sessionConnection, $loggerFactory);
	}

	#endregion
}


// private AppConfiguration $appConfig,
// private ILoggerFactory $loggerFactory
