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
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	private static string $environment = '';
	private static string $revision = '';

	public static function initialize(string $environment, string $revision): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$environment = $environment;
		self::$revision = $revision;
	}

	public static function get(): string
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		return self::$environment;
	}

	public static function is(string $environment): bool
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

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
