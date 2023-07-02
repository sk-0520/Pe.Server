<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;

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

	public static function getSqliteFilePath(string $connection): string {
		list($db, $target) = Text::split($connection, ':', 2);

		if($db !== 'sqlite') {
			throw new InvalidOperationException($db);
		}

		return $target;
	}

	#endregion

	#region DatabaseConnection

	public function open(): DatabaseContext
	{
		$database = parent::open();

		$database->execute('PRAGMA foreign_keys = ON;');

		return $database;
	}

	#endregion
}
