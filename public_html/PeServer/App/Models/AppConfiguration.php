<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\I18n;
use PeServer\Core\Environment;
use PeServer\Core\FileUtility;
use PeServer\Core\Log\Logging;
use PeServer\Core\PathUtility;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Configuration;
use PeServer\Core\Store\StorePack;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Store\SpecialStore;
use PeServer\App\Models\AppDatabaseCache;


abstract class AppConfiguration
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker
	 */
	private static InitializeChecker $initializeChecker;

	/**
	 * 設定データ。
	 *
	 * @var array<mixed>
	 */
	public static $config;

	/**
	 * ルートディレクトリ。
	 *
	 * @var string
	 */
	public static $rootDirectoryPath;
	/**
	 * ベースディレクトリ。
	 *
	 * 基本的にこちらを使っておけば問題なし。
	 *
	 * @var string
	 */
	public static $baseDirectoryPath;

	/**
	 * 設定ファイル置き場。
	 *
	 * @var string
	 */
	public static string $settingDirectoryPath;

	public static StorePack $storePack;

	/**
	 * 設定ファイル読み込み。
	 *
	 * @param string $rootDirectoryPath
	 * @param string $baseDirectoryPath
	 * @param string $environment
	 * @return array<mixed>
	 */
	private static function load(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $fileName): array
	{
		$configuration = new Configuration($environment);
		$setting = $configuration->load(self::$settingDirectoryPath, $fileName);

		return $configuration->replace(
			$setting,
			[
				'ROOT' => $rootDirectoryPath,
				'BASE' => $baseDirectoryPath,
				'ENV' => $environment
			],
			'$(',
			')'
		);
	}

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, SpecialStore $specialStore): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$settingDirectoryPath = PathUtility::joinPath($baseDirectoryPath, 'config');

		$appConfig = self::load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'setting.json');
		$i18nConfig = self::load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'i18n.json');

		$storeOptions = StoreConfiguration::build();
		$storePack = new StorePack($specialStore, $storeOptions);

		Logging::initialize($specialStore, $appConfig['logging']);

		Template::initialize($rootDirectoryPath, $baseDirectoryPath, 'App/Views', 'data/temp/views', $specialStore);
		I18n::initialize($i18nConfig);

		AppDatabaseCache::initialize($appConfig['cache']['database']);

		self::$config = $appConfig;
		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
		self::$storePack = $storePack;
	}
}
