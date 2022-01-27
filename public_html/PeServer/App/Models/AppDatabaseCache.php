<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\FileUtility;
use PeServer\Core\Log\Logging;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Domain\PluginDomainDao;

abstract class AppDatabaseCache
{
	private const USER_INFORMATION = 'user.json';
	private const PLUGIN_INFORMATION = 'plugin.json';

	/**
	 * 初期化チェック
	 */
	private static InitializeChecker $initializeChecker;

	private static string $cacheDirectoryPath;

	public static function initialize(string $cacheDirectoryPath): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$cacheDirectoryPath = $cacheDirectoryPath;
	}

	private static function openDatabase(): IDatabaseContext
	{
		$logger = Logging::create('cache');
		return AppDatabase::open($logger);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $fileName
	 * @param array<mixed> $cache
	 * @return void
	 */
	private static function exportCache(string $fileName, array $cache): void
	{
		$filePath = FileUtility::joinPath(self::$cacheDirectoryPath, $fileName);
		FileUtility::createParentDirectoryIfNotExists($filePath);
		FileUtility::writeJsonFile($filePath, $cache);
	}

	/**
	 * ユーザー情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public static function exportUserInformation(): void
	{
		self::$initializeChecker->throwIfNotInitialize();

		$context = self::openDatabase();
		$userDomainDao = new UserDomainDao($context);
		$items = $userDomainDao->selectCacheItems();

		self::exportCache(self::USER_INFORMATION, $items);
	}

	/**
	 * プラグイン情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public static function exportPluginInformation(): void
	{
		self::$initializeChecker->throwIfNotInitialize();

		$context = self::openDatabase();
		$userDomainDao = new PluginDomainDao($context);
		$items = $userDomainDao->selectCacheItems();

		self::exportCache(self::PLUGIN_INFORMATION, $items);
	}
}
