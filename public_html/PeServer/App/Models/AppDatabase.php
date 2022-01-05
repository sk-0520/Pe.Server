<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\ILogger;
use PeServer\Core\Database\Database;
use PeServer\Core\Log\Logging;

class AppDatabase extends Database
{
	private static ?ILogger $logger;

	public static function open(?ILogger $logger = null): Database
	{
		$persistence = AppConfiguration::$json['persistence'];

		return new Database(
			$persistence['connection'],
			$persistence['user'],
			$persistence['password'],
			[],
			self::$logger ??= $logger ?? Logging::create('database')
		);
	}
}
