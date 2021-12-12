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

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		AppConfiguration::initialize($rootDirectoryPath, $baseDirectoryPath, $environment);
		Template::initialize($rootDirectoryPath, $baseDirectoryPath, $environment);
	}
}
