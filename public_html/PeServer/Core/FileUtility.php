<?php

declare(strict_types=1);

namespace PeServer\Core;

class FileUtility
{
	public static function join(string $basePath, string ...$addPaths): string
	{
		$paths = array_merge([$basePath], $addPaths);

		$joinedPath = implode(DIRECTORY_SEPARATOR, $paths);
		return $joinedPath;
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
