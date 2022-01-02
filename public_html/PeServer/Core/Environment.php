<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\CoreError;

abstract class Environment
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	private static string $environment = '';

	public static function initialize(string $environment)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$environment = $environment;
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
}
