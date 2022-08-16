<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\IO\File;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Domain\PluginDomainDao;
use PeServer\Core\Database\IDatabaseConnection;

class AppDatabaseCache
{
	private const USER_INFORMATION = 'user.json';
	private const PLUGIN_INFORMATION = 'plugin.json';

	private string $cacheDirectoryPath;

	public function __construct(
		AppConfiguration $config,
		private IDatabaseConnection $connection
	) {
		$this->cacheDirectoryPath = $config->setting['cache']['database'];
	}

	private function openDatabase(): IDatabaseContext
	{
		return $this->connection->open();
	}

	/**
	 * Undocumented function
	 *
	 * @param string $fileName
	 * @param array<mixed> $cache
	 * @return void
	 */
	private function exportCache(string $fileName, array $cache): void
	{
		$filePath = Path::combine($this->cacheDirectoryPath, $fileName);
		Directory::createParentDirectoryIfNotExists($filePath);
		File::writeJsonFile($filePath, $cache);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $fileName
	 * @return array<mixed>
	 */
	private function readCache(string $fileName): array
	{
		$filePath = Path::combine($this->cacheDirectoryPath, $fileName);
		$result = File::readJsonFile($filePath);
		return $result;
	}

	/**
	 * ユーザー情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public function exportUserInformation(): void
	{
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
	public function exportPluginInformation(): void
	{
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
	public function readPluginInformation(): array
	{
		$items = self::readCache(self::PLUGIN_INFORMATION);

		return array_map(function ($i) {
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
