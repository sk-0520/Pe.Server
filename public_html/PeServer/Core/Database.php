<?php

declare(strict_types=1);

namespace PeServer\Core;

class Database
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	private static $databaseConfiguration; // @phpstan-ignore-line

	public static function initialize(array $databaseConfiguration)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$databaseConfiguration = $databaseConfiguration;
	}
}
