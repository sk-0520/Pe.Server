<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Log\ILoggerFactory;

class AppDatabaseConnection extends DatabaseConnection
{
	public function __construct(
		AppConfiguration $config,
		ILoggerFactory $loggerFactory
	) {
		$persistence = $config->setting->persistence->default;
		$connectionSetting =  new ConnectionSetting(
			$persistence->connection,
			$persistence->user,
			$persistence->password,
			[]
		);

		parent::__construct($connectionSetting, $loggerFactory);
	}

	#region DatabaseConnection

	public function open(): DatabaseContext
	{
		$database = parent::open();

		$database->execute('PRAGMA foreign_keys = ON;');

		return $database;
	}

	#endregion
}
