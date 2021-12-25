<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * オートローダー。
 */
abstract class AutoLoader
{
	/**
	 * 読み込みベースパス。
	 *
	 * @var string[]
	 */
	private static $_baseDirectoryPaths;

	/**
	 * 読み込み対象パターン。
	 *
	 * @var string
	 */
	private static $_includePattern;

	/**
	 * 初期化。
	 *
	 * @param string[] $baseDirectoryPaths
	 * @return void
	 */
	public static function initialize(array $baseDirectoryPaths, string $includePattern)
	{
		self::$_baseDirectoryPaths = $baseDirectoryPaths;
		self::$_includePattern = $includePattern;

		spl_autoload_register([__CLASS__, 'load']);
	}

	private static function load(string $className): void
	{
		if (!preg_match(self::$_includePattern, $className)) {
			return;
		}

		foreach (self::$_baseDirectoryPaths as $baseDirectoryPath) {
			$fileBasePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
			$filePath = $baseDirectoryPath . DIRECTORY_SEPARATOR . $fileBasePath . '.php';

			if (file_exists($filePath)) {
				require_once $filePath;
			}
		}
	}
}
