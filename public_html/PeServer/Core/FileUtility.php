<?php

declare(strict_types=1);

namespace PeServer\Core;

class FileUtility
{
	/**
	 * 絶対パスへ変換。
	 *
	 * @param string $path パス。
	 * @return string 絶対パス。
	 */
	public static function toAbsolutePath(string $path): string
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

	/**
	 * パスの結合。
	 *
	 * @param string $basePath ベースとなるパス。
	 * @param string ...$addPaths 連結していくパス。
	 * @return string 結合後のパス。正規化される。
	 */
	public static function joinPath(string $basePath, string ...$addPaths): string
	{
		$paths = array_merge([$basePath], array_map(function ($s) {
			return trim($s, '/\\');
		}, $addPaths));
		$paths = array_filter($paths, function ($v, $k) {
			return !StringUtility::isNullOrEmpty($v) && ($k === 0 ? true :  $v !== '/' && $v !== '\\');
		}, ARRAY_FILTER_USE_BOTH);


		$joinedPath = implode(DIRECTORY_SEPARATOR, $paths);
		return self::toAbsolutePath($joinedPath);
	}

	/**
	 * JSONとしてファイル読み込み
	 *
	 * @param string $path パス
	 * @param boolean $associative 連想配列として扱うか
	 * @return array|stdClass|null 応答JSON
	 */
	public static function readJsonFile(string $path, bool $associative = true)
	{
		$content = file_get_contents($path);
		return json_decode($content, $associative);
	}

	/**
	 * ディレクトリが存在しない場合に作成する。
	 *
	 * ディレクトリは再帰的に作成される。
	 *
	 * @param string $directoryPath ディレクトリパス
	 * @return void
	 */
	public static function createDirectoryIfNotExists(string $directoryPath)
	{
		if (!file_exists($directoryPath)) {
			mkdir($directoryPath, 0777, true);
		}
	}

	/**
	 * 対象パスの親ディレクトリが存在しない場合に親ディレクトリを作成する。
	 *
	 * ディレクトリは再帰的に作成される。
	 *
	 * @param string $path 対象パス（メソッド自体はファイルパスとして使用することを前提としている）
	 * @return void
	 */
	public static function createParentDirectoryIfNotExists(string $path)
	{
		self::createDirectoryIfNotExists(dirname($path));
	}
}
