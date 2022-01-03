<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * オートローダー。
 *
 * NOTE: なにがあってもPHP標準関数ですべて処理すること。
 */
abstract class AutoLoader
{
	/**
	 * 読み込みベースパス。
	 *
	 * @var string[]
	 */
	private static $baseDirectoryPaths;

	/**
	 * 読み込み対象パターン。
	 *
	 * @var string
	 */
	private static $includePattern;

	/**
	 * 初期化。
	 *
	 * @param string[] $baseDirectoryPaths
	 * @return void
	 */
	public static function initialize(array $baseDirectoryPaths, string $includePattern)
	{
		self::$baseDirectoryPaths = $baseDirectoryPaths;
		self::$includePattern = $includePattern;

		spl_autoload_register([__CLASS__, 'load']);
	}

	private static function load(string $className): void
	{
		if (!preg_match(self::$includePattern, $className)) {
			return;
		}

		foreach (self::$baseDirectoryPaths as $baseDirectoryPath) {
			$fileBasePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
			$filePath = $baseDirectoryPath . DIRECTORY_SEPARATOR . $fileBasePath . '.php';

			if (is_file($filePath)) {
				require_once $filePath;
			}
		}
	}
}
