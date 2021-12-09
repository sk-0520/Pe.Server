<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\FileUtility;
use \PeServer\Core\Template;
use \PeServer\App\Models\AppConfiguration;

class Initializer
{
	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		AppConfiguration::initialize($appDirectoryPath, $baseDirectoryPath, $environment);
		Template::initialize($appDirectoryPath, $baseDirectoryPath, $environment);
	}
}

