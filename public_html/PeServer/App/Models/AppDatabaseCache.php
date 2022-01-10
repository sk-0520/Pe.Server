<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\InitializeChecker;


abstract class AppDatabaseCache
{
	private const USER_INFORMATION = 'user.json'; // @phpstan-ignore-line
	private const PLUGIN_INFORMATION = 'plugin.json'; // @phpstan-ignore-line

	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	private static string $cacheDirectoryPath; // @phpstan-ignore-line

	public static function initialize(string $cacheDirectoryPath): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$cacheDirectoryPath = $cacheDirectoryPath;
	}

	/**
	 * ユーザー情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public static function exportUserInformation()
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access
		//TODO
	}

	/**
	 * プラグイン情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public static function exportPluginInformation()
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access
		//TODO
	}
}
