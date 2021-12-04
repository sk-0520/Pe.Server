<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\FileUtility;
use \PeServer\App\Models\AppConfiguration;

class Initializer
{
	public static function initialize(string $baseDirectoryPath, string $environment)
	{
		$settingFilePath = FileUtility::join($baseDirectoryPath, 'PeServer', 'config', "setting.{$environment}.conf");
		AppConfiguration::initialize($baseDirectoryPath, $settingFilePath);
	}
}

