<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\InitialValue;
use PeServer\Core\Throws\CoreError;

/**
 * 環境情報。
 */
abstract class Environment
{
	/**
	 * 初期化チェック
	 */
	private static InitializeChecker $initializeChecker;

	private static string $environment = InitialValue::EMPTY_STRING;
	private static string $revision = InitialValue::EMPTY_STRING;

	public static function initialize(string $environment, string $revision, string $language): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$environment = $environment;
		self::$revision = $revision;
	}

	public static function setLanguage(string $language): bool
	{
		return (bool)mb_language($language);
	}
	public static function getLanguage(): string
	{
		return (string)mb_language();
	}

	public static function get(): string
	{
		self::$initializeChecker->throwIfNotInitialize();

		return self::$environment;
	}

	public static function is(string $environment): bool
	{
		self::$initializeChecker->throwIfNotInitialize();

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
}
