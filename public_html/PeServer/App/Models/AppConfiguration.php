<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \Error;
use PeServer\Core\FileUtility;
use PeServer\Core\Log\Logging;
use PeServer\Core\Database;
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
	public static $json;

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
	 * 設定データの値置き換え。
	 *
	 * @param array<mixed> $array
	 * @param string $rootDirectoryPath
	 * @param string $baseDirectoryPath
	 * @param string $environment
	 * @return array<mixed>
	 */
	private static function replaceArray(array $array, string $rootDirectoryPath, string $baseDirectoryPath, string $environment): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::replaceArray($value, $rootDirectoryPath, $baseDirectoryPath, $environment);
			} else if (is_string($value)) {
				$array[$key] = StringUtility::replaceMap($value, [
					'ROOT' => $rootDirectoryPath,
					'BASE' => $baseDirectoryPath,
					'ENV' => $environment
				], '<|', '|>');
			}
		}

		return $array;
	}

	/**
	 * 設定ファイル読み込み。
	 *
	 * @param string $rootDirectoryPath
	 * @param string $baseDirectoryPath
	 * @param string $environment
	 * @return array<mixed>
	 */
	private static function load(string $rootDirectoryPath, string $baseDirectoryPath, string $environment): array
	{
		$settingDirPath = FileUtility::joinPath($baseDirectoryPath, 'config');

		$baseSettingFilePath = FileUtility::joinPath($settingDirPath, 'setting.json');
		/** @var array<mixed> */
		$baseSettingJson = FileUtility::readJsonFile($baseSettingFilePath);

		$json = array();

		$envSettingFilePath = FileUtility::joinPath($settingDirPath, "setting.$environment.json");
		if (file_exists($envSettingFilePath)) {
			/** @var array<mixed> */
			$envSettingJson = FileUtility::readJsonFile($envSettingFilePath);
			$json = array_replace_recursive($baseSettingJson, $envSettingJson);
		} else {
			$json = $baseSettingJson;
		}

		return self::replaceArray($json, $rootDirectoryPath, $baseDirectoryPath, $environment);
	}

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $revision): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		$json = self::load($rootDirectoryPath, $baseDirectoryPath, $environment);

		Logging::initialize($json['logging']);

		Template::initialize($rootDirectoryPath, $baseDirectoryPath, 'App/Views', 'data/temp/views', $environment, $revision);
		I18n::initialize($json['i18n']);

		self::$json = $json;
		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
	}
}
