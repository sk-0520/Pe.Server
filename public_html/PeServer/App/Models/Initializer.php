<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\FileUtility;
use \PeServer\App\Models\AppConfiguration;
use \PeServer\App\Models\Template;

class Initializer
{
	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		AppConfiguration::initialize($appDirectoryPath, $baseDirectoryPath, $environment);
		Template::initialize($appDirectoryPath, $baseDirectoryPath, $environment);
	}
}

