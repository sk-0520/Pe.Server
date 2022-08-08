<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\I18n;
use PeServer\Core\Environment;
use PeServer\Core\Log\Logging;
use PeServer\Core\PathUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Store\Stores;
use PeServer\Core\Configuration;
use PeServer\Core\Store\StorePack;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Store\SpecialStore;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\Core\IOUtility;

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
	public static array $config;

	/**
	 * ルートディレクトリ。
	 *
	 * @var string
	 */
	public static string $rootDirectoryPath;
	/**
	 * ベースディレクトリ。
	 *
	 * 基本的にこちらを使っておけば問題なし。
	 *
	 * @var string
	 */
	public static string $baseDirectoryPath;

	/**
	 * URL ベースパス。
	 *
	 * @var string
	 */
	public static string $urlBasePath;

	/**
	 * 設定ファイル置き場。
	 *
	 * @var string
	 */
	public static string $settingDirectoryPath;

	public static Stores $stores;

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

	/**
	 * 初期化。
	 *
	 * @param string $rootDirectoryPath 公開ルートディレクトリ
	 * @param string $baseDirectoryPath `\PeServer\*` のルートディレクトリ
	 * @param SpecialStore $specialStore
	 */
	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $urlBasePath, SpecialStore $specialStore): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$settingDirectoryPath = PathUtility::combine($baseDirectoryPath, 'config');

		$tempDirectoryPath = PathUtility::combine($baseDirectoryPath, 'data/temp/buckets');
		IOUtility::setTemporaryDirectory($tempDirectoryPath);

		$appConfig = self::load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'setting.json');
		$i18nConfig = self::load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'i18n.json');

		$storeOptions = StoreConfiguration::build(ArrayUtility::getOr($appConfig, 'store', null));
		$stores = new Stores($specialStore, $storeOptions);

		Logging::initialize($stores, $appConfig['logging']);

		Template::initialize($stores, $rootDirectoryPath, $baseDirectoryPath, $urlBasePath, 'App/Views', 'data/temp/views');
		I18n::initialize($i18nConfig);

		AppDatabaseCache::initialize($appConfig['cache']['database']);

		self::$config = $appConfig;
		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
		self::$urlBasePath = $urlBasePath;
		self::$stores = $stores;
	}
}
