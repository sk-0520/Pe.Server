<?php

declare(strict_types=1);

namespace PeServer\Core;

class Database
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker
	 */
	private static $initializeChecker;

	private static $databaseConfiguration;

	public static function initialize(array $databaseConfiguration)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$databaseConfiguration = $databaseConfiguration;
	}
}
