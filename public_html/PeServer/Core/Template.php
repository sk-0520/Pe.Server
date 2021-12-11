<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Smarty;

class Template
{
	private static $appDirectoryPath;

	public static function initialize(string $appDirectoryPath, string $baseDirectoryPath, string $environment)
	{
		self::$appDirectoryPath = $appDirectoryPath;
	}

	public static function createTemplate(string $baseName): Smarty
	{
		$smarty = new Smarty();
		$smarty->addTemplateDir(self::$appDirectoryPath . "/App/Views/$baseName/");
		$smarty->addTemplateDir(self::$appDirectoryPath . "/App/Views/");
		$smarty->compile_dir  = self::$appDirectoryPath . "/data/temp/views/c/$baseName/";
		$smarty->cache_dir    = self::$appDirectoryPath . "/data/temp/views/t/$baseName/";

		return $smarty;
	}
}
