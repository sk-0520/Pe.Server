<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\Cache\CacheException;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Cache\UserCache;
use PeServer\App\Models\Dao\Domain\PluginDomainDao;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\Core\Binary;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Serialization\BuiltinSerializer;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Throws\SerializeException;
use PeServer\Core\TypeUtility;

class AppDatabaseCache
{
	#region define

	private const USER_INFORMATION = 'user.cache';
	private const PLUGIN_INFORMATION = 'plugin.cache';

	#endregion

	#region variable

	private string $cacheDirectoryPath;
	private SerializerBase $serializer;

	#endregion

	public function __construct(
		AppConfiguration $config,
		private IDatabaseConnection $connection
	) {
		$this->cacheDirectoryPath = $config->setting->cache->database;
		$this->serializer = new BuiltinSerializer();
	}

	private function openDatabase(): IDatabaseContext
	{
		return $this->connection->open();
	}

	/**
	 * オブジェクトをキャシュデータとして出力。
	 *
	 * @param string $fileName
	 * @param object $object
	 * @return void
	 */
	private function exportCache(string $fileName, object $object): void
	{
		$filePath = Path::combine($this->cacheDirectoryPath, $fileName);
		Directory::createParentDirectoryIfNotExists($filePath);

		$binary = $this->serializer->save($object);
		File::writeContent($filePath, $binary);
	}

	/**
	 * キャシュデータをオブジェクトとして読み込み。
	 *
	 * @template T of object
	 * @param string $fileName
	 * @param string $className
	 * @phpstan-param class-string<T> $className
	 * @return object
	 * @phpstan-return T
	 */
	private function readCache(string $fileName, string $className): object
	{
		$filePath = Path::combine($this->cacheDirectoryPath, $fileName);
		$binary = File::readContent($filePath);
		$object = $this->serializer->load($binary);
		if (!is_array($object) && is_a($object, $className)) {
			/** @phpstan-var T */
			return $object;
		}

		throw new SerializeException(TypeUtility::getType($object));
	}

	/**
	 * ユーザー情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public function exportUserInformation(): void
	{
		$context = $this->openDatabase();
		$userDomainDao = new UserDomainDao($context);

		$cache = new UserCache(
			$userDomainDao->selectCacheItems()
		);

		self::exportCache(self::USER_INFORMATION, $cache);
	}

	/**
	 * プラグイン情報をキャッシュ出力。
	 *
	 * @return void
	 */
	public function exportPluginInformation(): void
	{
		$context = $this->openDatabase();
		$userDomainDao = new PluginDomainDao($context);

		$cache = new PluginCache(
			$userDomainDao->selectCacheCategories(),
			$userDomainDao->selectCacheItems()
		);

		self::exportCache(self::PLUGIN_INFORMATION, $cache);
	}

	/**
	 * キャッシュを全出力。
	 *
	 * @return string[]
	 */
	public function exportAll(): array
	{
		$this->exportUserInformation();
		$this->exportPluginInformation();

		return [
			'user_information',
			'plugin_information',
		];
	}

	/**
	 * プラグイン情報のキャッシュ取得。
	 *
	 * @return PluginCache
	 */
	public function readPluginInformation(): PluginCache
	{
		return self::readCache(self::PLUGIN_INFORMATION, PluginCache::class);
	}

	/**
	 * ユーザー情報のキャッシュ取得。
	 *
	 * @return UserCache
	 */
	public function readUserInformation(): UserCache
	{
		return self::readCache(self::USER_INFORMATION, UserCache::class);
	}
}
