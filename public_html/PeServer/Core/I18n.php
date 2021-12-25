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
	 * Undocumented variable
	 *
	 * @var array<string,array<string,string>>
	 */
	private static array $_i18nConfiguration;

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

		self::$_i18nConfiguration = $i18nConfiguration;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $message
	 * @param array<int|string,int|string> $parameters
	 * @return string
	 */
	public static function message(string $message, array $parameters = array()): string
	{
		self::$_initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		/** @var array<string,string> */
		$params = [];
		foreach ($parameters as $key => $value) {
			$params[strval($key)] = strval($value);
		}

		if (isset(self::$_i18nConfiguration['*'][$message])) {
			$message = self::$_i18nConfiguration['*'][$message];
		}

		return StringUtility::replaceMap($message, $params);
	}
}
