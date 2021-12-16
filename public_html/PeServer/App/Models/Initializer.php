<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\FileUtility;
use \PeServer\Core\InitializeChecker;
use \PeServer\Core\Mvc\Template;
use \PeServer\App\Models\AppConfiguration;

abstract class Initializer
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		AppConfiguration::initialize($rootDirectoryPath, $baseDirectoryPath, $environment);
		Template::initialize($rootDirectoryPath, $baseDirectoryPath, $environment);
	}
}
