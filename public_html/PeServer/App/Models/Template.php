<?php

declare(strict_types=1);

namespace PeServer\App\Models;

require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

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
		$smarty->compile_dir  = self::$appDirectoryPath . "/temp/views/c/$baseName/";
		$smarty->cache_dir    = self::$appDirectoryPath . "/temp/views/t/$baseName/";

		return $smarty;
	}
}
