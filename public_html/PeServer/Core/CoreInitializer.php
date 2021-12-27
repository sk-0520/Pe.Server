<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class CoreInitializer
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	public static function initialize(): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		mb_internal_encoding("UTF-8");
	}
}
