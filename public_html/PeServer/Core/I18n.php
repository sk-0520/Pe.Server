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

	/**
	 * Undocumented function
	 *
	 * @param array<string,mixed> $i18nConfiguration
	 * @return void
	 */
	public static function initialize(array $i18nConfiguration): void
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();
	}

	/**
	 * Undocumented function
	 *
	 * @param string $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public static function message(string $message, ...$parameters): string
	{
		self::$_initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		return $message;
	}
}
