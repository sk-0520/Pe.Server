<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use Error;
use \PeServer\Core\FileUtility;
use \PeServer\Core\Log\Logging;
use \PeServer\Core\Database;
use \PeServer\Core\InitializeChecker;
use \PeServer\Core\StringUtility;

class AppConfiguration
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	/**
	 * 環境情報。
	 *
	 * @var string
	 */
	private static $environment;
	/**
	 * 設定データ。
	 *
	 * @var array
	 */
	public static $json; // @phpstan-ignore-line

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

	private static function replaceArray(array $array, string $rootDirectoryPath, string $baseDirectoryPath, string $environment): array // @phpstan-ignore-line
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::replaceArray($value, $rootDirectoryPath, $baseDirectoryPath, $environment);
			} else if (is_string($value)) {
				$array[$key] = StringUtility::replaceMap($value, [
					'ROOT' => $rootDirectoryPath,
					'BASE' => $baseDirectoryPath,
					'ENV' => $environment
				], '<', '>');
			}
		}

		return $array;
	}

	private static function load(string $rootDirectoryPath, string $baseDirectoryPath, string $environment): array // @phpstan-ignore-line
	{
		$settingDirPath = FileUtility::joinPath($baseDirectoryPath, 'config');

		$baseSettingFilePath = FileUtility::joinPath($settingDirPath, 'setting.json');
		/** @var array */
		$baseSettingJson = FileUtility::readJsonFile($baseSettingFilePath); // @phpstan-ignore-line

		$json = array();

		$envSettingFilePath = FileUtility::joinPath($settingDirPath, "setting.$environment.json");
		if (file_exists($envSettingFilePath)) {
			/** @var array */
			$envSettingJson = FileUtility::readJsonFile($envSettingFilePath); // @phpstan-ignore-line
			$json = array_replace_recursive($baseSettingJson, $envSettingJson);
		} else {
			$json = $baseSettingJson;
		}

		return self::replaceArray($json, $rootDirectoryPath, $baseDirectoryPath, $environment);
	}

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		$json = self::load($rootDirectoryPath, $baseDirectoryPath, $environment);

		Logging::initialize($json['logging']);
		Database::initialize($json['persistence']);

		self::$environment = $environment;
		self::$json = $json;
		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
	}

	public static function isEnvironment(string $environment): bool
	{
		self::$initializeChecker->throwIfNotInitialize();

		return self::$environment === $environment;
	}

	public static function isProductionEnvironment(): bool
	{
		return self::isEnvironment('production');
	}
	public static function isDevelopmentEnvironment(): bool
	{
		return self::isEnvironment('development');
	}
	public static function isTestEnvironment(): bool
	{
		return self::isEnvironment('test');
	}
}
