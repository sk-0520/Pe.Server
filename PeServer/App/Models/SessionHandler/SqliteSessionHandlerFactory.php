<?php

declare(strict_types=1);

namespace PeServer\App\Models\SessionHandler;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Store\ISessionHandlerFactory;
use PeServer\Core\Store\SessionOptions;

class SqliteSessionHandlerFactory implements ISessionHandlerFactory
{
	#region function

	/**
	 * `SqliteSessionHandlerFactory` か。
	 *
	 * @param class-string<ISessionHandlerFactory> $name
	 * @return bool
	 * @assert-if-true class-string<SqliteSessionHandlerFactory> $name
	 */
	public static function isSqliteFactory(string $name)
	{
		if (!class_exists($name)) {
			return false;
		}

		if($name === SqliteSessionHandlerFactory::class) {
			return true;
		}

		return is_subclass_of($name, SqliteSessionHandlerFactory::class);
	}

	#endregion

	#region ISessionHandlerFactory

	public function create(SessionOptions $options): SqliteSessionHandler
	{
		$connection = SqliteSessionHandler::createConnection($options->savePath, null, LoggerFactory::createNullFactory());
		$handler = new SqliteSessionHandler($connection, LoggerFactory::createNullFactory());

		return $handler;
	}

	#endrgieon
}
