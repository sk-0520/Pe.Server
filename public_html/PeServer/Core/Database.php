<?php

declare(strict_types=1);

namespace PeServer\Core;

class Database
{
	private static $databaseConfiguration;

	public static function initialize(array $databaseConfiguration)
	{
		self::$databaseConfiguration = $databaseConfiguration;
	}
}
