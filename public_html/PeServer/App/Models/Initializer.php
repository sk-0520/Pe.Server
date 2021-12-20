<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\CoreInitializer;
use \PeServer\Core\InitializeChecker;
use \PeServer\App\Models\AppConfiguration;

abstract class Initializer
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $_initializeChecker;

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $revision): void
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();

		CoreInitializer::initialize();
		AppConfiguration::initialize($rootDirectoryPath, $baseDirectoryPath, $environment, $revision);
	}
}
