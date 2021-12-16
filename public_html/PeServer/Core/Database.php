<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class Database
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	/**
	 * DB接続設定
	 *
	 * @var array
	 */
	private static $databaseConfiguration; // @phpstan-ignore-line

	public static function initialize(array $databaseConfiguration): void // @phpstan-ignore-line
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$databaseConfiguration = $databaseConfiguration;
	}
}
