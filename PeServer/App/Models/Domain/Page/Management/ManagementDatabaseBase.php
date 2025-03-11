<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\SessionHandler\SqliteSessionHandler;
use PeServer\App\Models\SessionHandler\SqliteSessionHandlerFactory;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Store\SessionHandlerFactoryUtility;
use PeServer\Core\Throws\NotImplementedException;

abstract class ManagementDatabaseBase extends PageLogicBase
{
	protected function __construct(LogicParameter $parameter, protected AppConfiguration $appConfig)
	{
		parent::__construct($parameter);
	}

	#region function

	protected function getTargetDatabaseId(): string
	{
		return $this->getRequest("database");
	}

	protected function getTargetDatabaseConnection(): IDatabaseConnection
	{
		$targetDatabase = $this->getTargetDatabaseId();

		switch ($targetDatabase) {
			case "main":
				return $this->databaseConnection;

			case "session":
				if (SessionHandlerFactoryUtility::isFactory($this->appConfig->setting->store->session->handlerFactory)) {
					$sessionConnection = SqliteSessionHandler::createConnection($this->appConfig->setting->store->session->save, null, LoggerFactory::createNullFactory());
					return $sessionConnection;
				}

			default:
				throw new NotImplementedException($targetDatabase);
		}
	}

	protected function getTargetContext(): DatabaseContext
	{
		$targetDatabase = $this->getTargetDatabaseId();

		switch ($targetDatabase) {
			case "main":
				return $this->openDatabase();

			case "session":
				if (SessionHandlerFactoryUtility::isFactory($this->appConfig->setting->store->session->handlerFactory)) {
					$sessionConnection = SqliteSessionHandler::createConnection($this->appConfig->setting->store->session->save, null, LoggerFactory::createNullFactory());
					return $sessionConnection->open();
				}

			default:
				throw new NotImplementedException($targetDatabase);
		}
	}

	#endregion
}
