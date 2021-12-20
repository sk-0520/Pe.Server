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
	private static $_initializeChecker;

	public static function initialize(): void
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();

		mb_internal_encoding("UTF-8");
	}
}
