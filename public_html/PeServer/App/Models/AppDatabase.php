<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Database\Database;
use PeServer\App\Models\AppConfiguration;

class AppDatabase extends Database
{
	private static ILogger $logger;

	public static function open(?ILogger $logger = null): Database
	{
		$persistence = AppConfiguration::$config['persistence'];

		$database = new Database(
			$persistence['connection'],
			$persistence['user'],
			$persistence['password'],
			[],
			self::$logger ??= $logger ?? Logging::create('database')
		);

		$database->execute('PRAGMA foreign_keys = ON;');

		return $database;
	}
}
