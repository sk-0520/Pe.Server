<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use Error;
use PeServer\Core\FileUtility;
use PeServer\Core\Logging;
use PeServer\Core\Database;

class AppConfiguration
{
	public static $json;

	private static function load(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		$settingDirPath = FileUtility::join($appDirectoryPath, 'config');

		$baseSettingFilePath = FileUtility::join($settingDirPath, 'setting.json');
		$baseSettingJson = FileUtility::readJsonFile($baseSettingFilePath);
		if (is_null($baseSettingJson)) {
			throw new Error($baseSettingFilePath);
		}

		$json = array();

		$envSettingFilePath = FileUtility::join($settingDirPath, "setting.$environment.json");
		if (file_exists($envSettingFilePath)) {
			$envSettingJson = FileUtility::readJsonFile($envSettingFilePath);
			if (is_null($envSettingJson)) {
				throw new Error($envSettingFilePath);
			}
			$json = array_merge($baseSettingJson, $envSettingJson);
		} else {
			$json = $baseSettingJson;
		}

		//TODO: 値の置き換え処理

		return $json;
	}

	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		$json = self::load($appDirectoryPath, $baseDirectoryPath, $environment);

		Logging::initialize($json['logging']);
		Database::initialize($json['persistence']);

		self::$json = $json;
	}
}
