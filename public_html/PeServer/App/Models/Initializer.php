<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\FileUtility;
use \PeServer\Core\Template;
use \PeServer\Core\InitializeChecker;
use \PeServer\App\Models\AppConfiguration;

class Initializer
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker
	 */
	private static $initializeChecker;

	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		AppConfiguration::initialize($appDirectoryPath, $baseDirectoryPath, $environment);
		Template::initialize($appDirectoryPath, $baseDirectoryPath, $environment);
	}
}
