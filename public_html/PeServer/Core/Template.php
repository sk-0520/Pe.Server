<?php

declare(strict_types=1);

namespace PeServer\Core;

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
	private static $appDirectoryPath;

	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$appDirectoryPath = $appDirectoryPath;
	}

	public static function createTemplate(string $baseName): Smarty
	{
		self::$initializeChecker->throwIfNotInitialize();

		$smarty = new Smarty();
		$smarty->addTemplateDir(self::$appDirectoryPath . "/App/Views/$baseName/");
		$smarty->addTemplateDir(self::$appDirectoryPath . "/App/Views/");
		$smarty->compile_dir  = self::$appDirectoryPath . "/data/temp/views/c/$baseName/";
		$smarty->cache_dir    = self::$appDirectoryPath . "/data/temp/views/t/$baseName/";

		return $smarty;
	}
}
