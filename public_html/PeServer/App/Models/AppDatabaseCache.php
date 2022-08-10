<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\IO\File;
use PeServer\Core\IO\Directory;
use PeServer\Core\Log\Logging;
use PeServer\Core\IO\Path;
use PeServer\Core\InitializeChecker;
use PeServer\App\Models\Cache\PluginCache;
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
		$filePath = Path::combine(self::$cacheDirectoryPath, $fileName);
		Directory::createParentDirectoryIfNotExists($filePath);
		File::writeJsonFile($filePath, $cache);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $fileName
	 * @return array<mixed>
	 */
	private static function readCache(string $fileName): array
	{
		$filePath = Path::combine(self::$cacheDirectoryPath, $fileName);
		$result = File::readJsonFile($filePath);
		return $result;
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

	/**
	 * Undocumented function
	 *
	 * @return array<PluginCache>
	 */
	public static function readPluginInformation(): array
	{
		self::$initializeChecker->throwIfNotInitialize();

		$items = self::readCache(self::PLUGIN_INFORMATION);

		return array_map(function($i) {
			$item = new PluginCache();
			$item->pluginId = $i['pluginId'];
			$item->userId = $i['userId'];
			$item->pluginName = $i['pluginName'];
			$item->displayName = $i['displayName'];
			$item->state = $i['state'];
			$item->description = $i['description'];
			$item->urls = $i['urls'];

			return $item;
		}, $items);
	}
}
