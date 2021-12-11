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
	 * @var InitializeChecker
	 */
	private static $initializeChecker;

	private static $environment;
	public static $json;

	private static function replaceArray(array $array, string $appDirectoryPath, string $baseDirectoryPath, string $environment): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::replaceArray($value, $appDirectoryPath, $baseDirectoryPath, $environment);
			} else if (is_string($value)) {
				$array[$key] = StringUtility::replaceMap($value, [
					'APP' => $appDirectoryPath,
					'BASE' => $baseDirectoryPath,
					'ENV' => $environment
				], '<', '>');
			}
		}

		return $array;
	}

	private static function load(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		$settingDirPath = FileUtility::joinPath($appDirectoryPath, 'config');

		$baseSettingFilePath = FileUtility::joinPath($settingDirPath, 'setting.json');
		$baseSettingJson = FileUtility::readJsonFile($baseSettingFilePath);
		if (is_null($baseSettingJson)) {
			throw new Error($baseSettingFilePath);
		}

		$json = array();

		$envSettingFilePath = FileUtility::joinPath($settingDirPath, "setting.$environment.json");
		if (file_exists($envSettingFilePath)) {
			$envSettingJson = FileUtility::readJsonFile($envSettingFilePath);
			if (is_null($envSettingJson)) {
				throw new Error($envSettingFilePath);
			}
			$json = array_replace_recursive($baseSettingJson, $envSettingJson);
		} else {
			$json = $baseSettingJson;
		}

		return self::replaceArray($json, $appDirectoryPath, $baseDirectoryPath, $environment);
	}

	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		$json = self::load($appDirectoryPath, $baseDirectoryPath, $environment);

		Logging::initialize($json['logging']);
		Database::initialize($json['persistence']);

		self::$environment = $environment;
		self::$json = $json;
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
