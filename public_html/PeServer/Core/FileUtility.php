<?php

declare(strict_types=1);

namespace PeServer\Core;

class FileUtility
{
	public static function getAbsolutePath(string $path)
	{
		$targetPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $targetPath), 'mb_strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ($part === '.') {
				continue;
			}
			if ($part === '..') {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}

		$result = implode(DIRECTORY_SEPARATOR, $absolutes);
		if (mb_strlen($targetPath) && $targetPath[0] === DIRECTORY_SEPARATOR) {
			$result = DIRECTORY_SEPARATOR . $result;
		}

		return $result;
	}

	public static function join(string $basePath, string ...$addPaths): string
	{
		$paths = array_merge([$basePath], array_map(function ($s) {
			return trim($s, '/\\');
		}, $addPaths));
		$paths = array_filter($paths, function ($v, $k) {
			return !StringUtility::isNullOrEmpty($v) && ($k === 0 ? true :  $v !== '/' && $v !== '\\');
		}, ARRAY_FILTER_USE_BOTH);


		$joinedPath = implode(DIRECTORY_SEPARATOR, $paths);
		return self::getAbsolutePath($joinedPath);
	}

	public static function readJsonFile(string $path, bool $associative = true)
	{
		$content = file_get_contents($path);
		return json_decode($content, $associative);
	}

	public static function createDirectoryIfNotExists($directoryPath)
	{
		if (!file_exists($directoryPath)) {
			mkdir($directoryPath, 0777, true);
		}
	}

	public static function createParentDirectoryIfNotExists(string $path)
	{
		self::createDirectoryIfNotExists(dirname($path));
	}
}
