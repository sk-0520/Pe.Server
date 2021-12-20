<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class I18n
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $_initializeChecker;

	public static function initialize(array $i18nConfiguration): void // @phpstan-ignore-line
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();
	}

	public static function message(string $message, string ...$parameters): string
	{
		self::$_initializeChecker->throwIfNotInitialize();

		return $message;
	}
}
