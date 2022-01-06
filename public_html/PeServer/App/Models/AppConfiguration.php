<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \Error;
use PeServer\Core\Configuration;
use PeServer\Core\FileUtility;
use PeServer\Core\Log\Logging;
use PeServer\Core\Database\Database;
use PeServer\Core\I18n;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Mvc\Template;
use PeServer\Core\StringUtility;

abstract class AppConfiguration
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

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
	 * @var string
	 */
	public static $baseDirectoryPath;

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
		$settingDirPath = FileUtility::joinPath($baseDirectoryPath, 'config');

		$configuration = new Configuration($environment);
		$setting = $configuration->load($settingDirPath, $fileName);

		return $configuration->replace(
			$setting,
			[
				'ROOT' => $rootDirectoryPath,
				'BASE' => $baseDirectoryPath,
				'ENV' => $environment
			],
			[
				'head' => '<|',
				'tail' => '|>',
			]
		);
	}

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $revision): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		$appConfig = self::load($rootDirectoryPath, $baseDirectoryPath, $environment, 'setting.json');
		$i18nConfig = self::load($rootDirectoryPath, $baseDirectoryPath, $environment, 'i18n.json');

		Logging::initialize($appConfig['logging']);

		Template::initialize($rootDirectoryPath, $baseDirectoryPath, 'App/Views', 'data/temp/views', $revision);
		I18n::initialize($i18nConfig);

		self::$config = $appConfig;
		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
	}
}
