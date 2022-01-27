<?php

declare(strict_types=1);

namespace PeServer\Core;

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

	private static string $environment = '';
	private static string $revision = '';

	public static function initialize(string $environment, string $revision): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$environment = $environment;
		self::$revision = $revision;
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
		return self::$revision;
	}
}
