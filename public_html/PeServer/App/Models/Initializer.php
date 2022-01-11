<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\CoreInitializer;
use PeServer\Core\InitializeChecker;
use PeServer\App\Models\AppConfiguration;

abstract class Initializer
{
	/**
	 * 初期化チェック
	 */
	private static ?InitializeChecker $initializeChecker = null;

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $revision): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		CoreInitializer::initialize($environment, $revision);
		AppConfiguration::initialize($rootDirectoryPath, $baseDirectoryPath);
	}
}
