<?php

declare(strict_types=1);

namespace PeServer\Core;

// require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

use \Exception;
use \Smarty;
use \PeServer\Core\InitializeChecker;

/**
 * View側のテンプレート処理。
 *
 * 初期化の呼び出しが必須。
 */
class Template
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker
	 */
	private static $initializeChecker;
	private static $rootDirectoryPath;
	private static $baseDirectoryPath;

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
	}

	public static function createTemplate(string $baseName): Smarty // @phpstan-ignore-line
	{
		self::$initializeChecker->throwIfNotInitialize();

		// @phpstan-ignore-next-line
		$smarty = new Smarty();
		$smarty->addTemplateDir(self::$baseDirectoryPath . "/App/Views/$baseName/"); // @phpstan-ignore-line
		$smarty->addTemplateDir(self::$baseDirectoryPath . "/App/Views/"); // @phpstan-ignore-line
		$smarty->compile_dir  = self::$baseDirectoryPath . "/data/temp/views/c/$baseName/"; // @phpstan-ignore-line
		$smarty->cache_dir    = self::$baseDirectoryPath . "/data/temp/views/t/$baseName/"; // @phpstan-ignore-line

		return $smarty;
	}
}
