<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\Enforce;

/**
 * 環境情報。
 */
abstract class Environment
{
	#region variable
	/**
	 * 初期化チェック
	 */
	private static InitializeChecker|null $initializeChecker = null;

	private static string $environment = Text::EMPTY;
	private static string $revision = Text::EMPTY;

	#endregion

	#region function

	public static function initialize(string $locale, string $language, string $environment, string $revision): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		//setlocale(LC_ALL, $locale);

		self::$environment = $environment;
		self::$revision = $revision;
		//self::setLanguage($language);
	}

	// public static function setLanguage(string $language): bool
	// {
	// 	return (bool)mb_language($language);
	// }
	// public static function getLanguage(): string
	// {
	// 	return (string)mb_language();
	// }

	public static function get(): string
	{
		InitializeChecker::throwIfNotInitialize(self::$initializeChecker);

		return self::$environment;
	}

	public static function is(string $environment): bool
	{
		InitializeChecker::throwIfNotInitialize(self::$initializeChecker);

		return self::$environment === $environment;
	}

	public static function isProduction(): bool
	{
		return self::is('production');
	}
	public static function isDevelopment(): bool
	{
		return self::is('development');
	}
	public static function isTest(): bool
	{
		return self::is('test');
	}

	public static function getRevision(): string
	{
		if (self::isProduction()) {
			return self::$revision;
		}

		return (string)time();
	}

	/**
	 * 環境変数設定。
	 *
	 * `putenv` ラッパー。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @param string $value
	 * @return bool
	 * @see https://www.php.net/manual/function.putenv.php
	 */
	public static function setVariable(string $name, string $value): bool
	{
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line non-empty-string
			throw new ArgumentException($name);
		}

		return putenv($name . '=' . $value);
	}

	/**
	 * 環境変数取得。
	 *
	 * `getenv` ラッパー。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @return string|null 環境変数の値。取得できなかった場合に null。
	 * @see https://www.php.net/manual/function.getenv.php
	 */
	public static function getVariable(string $name): ?string
	{
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line non-empty-string
			throw new ArgumentException($name);
		}

		$result = getenv($name);
		if ($result === false) {
			return null;
		}

		return $result;
	}

	#endregion
}
