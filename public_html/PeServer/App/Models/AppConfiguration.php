<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\I18n;
use PeServer\Core\Environment;
use PeServer\Core\FileUtility;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Configuration;
use PeServer\Core\InitializeChecker;
use PeServer\App\Models\AppDatabaseCache;


abstract class AppConfiguration
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static ?InitializeChecker $initializeChecker = null;

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

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$settingDirectoryPath = FileUtility::joinPath($baseDirectoryPath, 'config');

		$appConfig = self::load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'setting.json');
		$i18nConfig = self::load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'i18n.json');

		Logging::initialize($appConfig['logging']);

		Template::initialize($rootDirectoryPath, $baseDirectoryPath, 'App/Views', 'data/temp/views');
		I18n::initialize($i18nConfig);

		AppDatabaseCache::initialize($appConfig['cache']['database']);

		self::$config = $appConfig;
		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
	}
}
