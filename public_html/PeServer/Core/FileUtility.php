<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\Throws\FileNotFoundException;
use \PeServer\Core\Throws\ParseException;

class FileUtility
{
	/**
	 * 絶対パスへ変換。
	 *
	 * @param string $path パス。
	 * @return string 絶対パス。
	 */
	public static function toCanonicalize(string $path): string
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
		return self::toCanonicalize($joinedPath);
	}

	/**
	 * JSONとしてファイル読み込み
	 *
	 * @param string $path パス
	 * @param boolean $associative 連想配列として扱うか
	 * @return array|\stdClass 応答JSON
	 */
	public static function readJsonFile(string $path, bool $associative = true) // @phpstan-ignore-line
	{
		$content = file_get_contents($path);
		if ($content === false) {
			throw new FileNotFoundException($path);
		}
		$json = json_decode($content, $associative);

		if (is_null($json)) {
			throw new ParseException($path);
		}

		return $json;
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

	/**
	 * ファイル/ディレクトリ一覧を取得する。
	 *
	 * @param string $directoryPath ディレクトリパス。
	 * @param boolean $recursive 再帰的に取得するか。
	 * @param boolean $directory
	 * @param boolean $file
	 * @return string[] ファイル一覧。
	 */
	private static function getChildrenCore(string $directoryPath, bool $directory, bool $file, bool $recursive): array
	{
		/** @var string[] */
		$files = [];
		$items = scandir($directoryPath);
		if ($items === false) {
			return $files;
		}

		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}

			$path = self::joinPath($directoryPath, $item);

			$isDir = is_dir($path);

			if ($isDir && $directory) {
				$files[] = $path;
			} else if (!$isDir && $file) {
				$files[] = $path;
			}

			if ($isDir && $recursive) {
				$files = array_merge($files, self::getChildrenCore($path, $directory, $file, $recursive));
			}
		}

		return $files;
	}

	/**
	 * ファイル/ディレクトリ一覧を取得する。
	 *
	 * @param string $directoryPath ディレクトリパス。
	 * @param boolean $recursive 再帰的に取得するか。
	 * @return string[] ファイル一覧。
	 */
	public static function getChildren(string $directoryPath, bool $recursive): array
	{
		return self::getChildrenCore($directoryPath, true, true, $recursive);
	}

	/**
	 * ファイル一覧を取得する。
	 *
	 * @param string $directoryPath ディレクトリパス。
	 * @param boolean $recursive 再帰的に取得するか。
	 * @return string[] ファイル一覧。
	 */
	public static function getFiles(string $directoryPath, bool $recursive): array
	{
		return self::getChildrenCore($directoryPath, false, true, $recursive);
	}

	/**
	 * ディレクトリ一覧を取得する。
	 *
	 * @param string $directoryPath ディレクトリパス。
	 * @param boolean $recursive 再帰的に取得するか。
	 * @return string[] ファイル一覧。
	 */
	public static function getDirectories(string $directoryPath, bool $recursive): array
	{
		return self::getChildrenCore($directoryPath, true, false, $recursive);
	}
	/**
	 * ディレクトリを削除する。
	 * ファイル・ディレクトリはすべて破棄される。
	 *
	 * @param string $directoryPath 削除ディレクトリ。
	 * @return void
	 */
	public static function removeDirectory(string $directoryPath): void
	{
		$files = self::getChildren($directoryPath, false);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::removeDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directoryPath);
	}

	/**
	 * ディレクトリを破棄・作成する
	 *
	 * @param string $directoryPath 対象ディレクトリ。
	 * @return void
	 */
	public static function cleanupDirectory(string $directoryPath): void
	{
		if (is_dir($directoryPath)) {
			self::removeDirectory($directoryPath);
		}
		mkdir($directoryPath, 0777, true);
	}

	/**
	 * バックアップ。
	 *
	 * !!未実装!!
	 *
	 * @param string $backupItem 対象ディレクトリ。
	 * @param string $baseDirectoryPath 対象ディレクトリ。
	 * @param string[] $targetPaths 対象ディレクトリ。
	 *
	*/
	public static function backupItems(string $backupItem, string $baseDirectoryPath, array $targetPaths): void
	{
		// NONE
	}
}
