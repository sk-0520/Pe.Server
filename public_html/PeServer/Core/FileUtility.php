<?php

declare(strict_types=1);

namespace PeServer\Core;

use stdClass;
use Exception;
use PeServer\Core\Bytes;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\FileNotFoundException;

abstract class FileUtility
{
	public const DIRECTORY_PERMISSIONS = 0777;

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
	 * ディレクトリパスを取得。
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getDirectoryPath(string $path): string
	{
		return dirname($path);
	}

	/**
	 * ファイル名を取得。
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getFileName(string $path): string
	{
		return basename($path);
	}

	/**
	 * 拡張子取得。
	 *
	 * @param string $path
	 * @param boolean $withDot . を付与するか。
	 * @return string
	 */
	public static function getFileExtension(string $path, bool $withDot = false): string
	{
		if (StringUtility::isNullOrWhiteSpace($path)) {
			return '';
		}

		$dotIndex = StringUtility::getLastPosition($path, '.');
		if ($dotIndex === -1) {
			return '';
		}

		$result = StringUtility::substring($path, $dotIndex);
		if ($withDot) {
			return $result;
		}

		if (!StringUtility::getByteCount($result)) {
			return '';
		}

		return StringUtility::substring($result, 1);
	}

	/**
	 * 拡張子を省いたファイル名を取得。
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getFileNameWithoutExtension(string $path): string
	{
		$fileName = self::getFileName($path);
		$dotIndex = StringUtility::getLastPosition($fileName, '.');
		if ($dotIndex === -1) {
			return $fileName;
		}

		return StringUtility::substring($fileName, 0, $dotIndex);
	}

	/**
	 * ファイルサイズを取得。
	 *
	 * @param string $path
	 * @return integer
	 */
	public static function getFileSize(string $path): int
	{
		$result = filesize($path);
		if ($result === false) {
			throw new IOException();
		}

		return $result;
	}

	/**
	 * パスから内容を取得。
	 *
	 * @param string $path
	 * @return Bytes
	 */
	public static function readContent(string $path): Bytes
	{
		/** @var string|false */
		$content = false;
		try {
			$content = file_get_contents($path);
		} catch (Exception $ex) {
			throw new IOException($ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($content === false) {
			throw new IOException($path);
		}

		return new Bytes($content);
	}

	/**
	 * 対象パスに指定データを書き込み。
	 *
	 * @param string $path
	 * @param mixed $data
	 * @param boolean $append
	 * @return void
	 */
	private static function saveContent(string $path, mixed $data, bool $append): void
	{
		$flag = $append ? FILE_APPEND : 0;
		$length = file_put_contents($path, $data, LOCK_EX | $flag);
		if ($length === false) {
			throw new IOException($path);
		}
	}

	/**
	 * 書き込み。
	 *
	 * @param string $path
	 * @param mixed $data
	 * @return void
	 */
	public static function writeContent(string $path, mixed $data): void
	{
		self::saveContent($path, $data, false);
	}

	/**
	 * 追記。
	 *
	 * @param string $path
	 * @param mixed $data
	 * @return void
	 */
	public static function appendContent(string $path, mixed $data): void
	{
		self::saveContent($path, $data, true);
	}

	/**
	 * JSONとしてファイル読み込み。
	 *
	 * @param string $path パス。
	 * @return array<mixed> 応答JSON。
	 * @throws IOException
	 * @throws ParseException パース失敗。
	 */
	public static function readJsonFile(string $path): array
	{
		$content = self::readContent($path);

		/** @var array<mixed>|null */
		$json = json_decode($content->getRaw(), true);

		if (is_null($json)) {
			throw new ParseException($path);
		}

		return $json;
	}

	/**
	 * JSONファイルとして出力。
	 *
	 * @param string $path
	 * @param array<mixed>|stdClass $data
	 * @return void
	 */
	public static function writeJsonFile(string $path, array|stdClass $data): void
	{
		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		if ($json === false) {
			throw new ParseException($path);
		}

		self::saveContent($path, $json, false);
	}

	/**
	 * ディレクトリ作成する。
	 *
	 * ディレクトリは再帰的に作成される。
	 *
	 * @param string $directoryPath ディレクトリパス。
	 * @param int $permissions
	 * @return bool
	 */
	public static function createDirectory(string $directoryPath, int $permissions = self::DIRECTORY_PERMISSIONS): bool
	{
		return mkdir($directoryPath, $permissions, true);
	}

	/**
	 * ディレクトリが存在しない場合に作成する。
	 *
	 * ディレクトリは再帰的に作成される。
	 *
	 * @param string $directoryPath ディレクトリパス
	 * @return bool
	 */
	public static function createDirectoryIfNotExists(string $directoryPath, int $permissions = self::DIRECTORY_PERMISSIONS): bool
	{
		if (!file_exists($directoryPath)) {
			return self::createDirectory($directoryPath, $permissions, true);
		}

		return false;
	}

	/**
	 * 対象パスの親ディレクトリが存在しない場合に親ディレクトリを作成する。
	 *
	 * ディレクトリは再帰的に作成される。
	 *
	 * @param string $path 対象パス（メソッド自体はファイルパスとして使用することを前提としている）
	 * @return bool
	 */
	public static function createParentDirectoryIfNotExists(string $path, int $permissions = self::DIRECTORY_PERMISSIONS): bool
	{
		return self::createDirectoryIfNotExists(dirname($path), $permissions);
	}

	/**
	 * ファイル・ディレクトリが存在するか。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 */
	public static function existsItem(string $path): bool
	{
		return file_exists($path);
	}

	/**
	 * ファイルが存在するか。
	 *
	 * self::existsItem より速い(file_existsよりis_fileの方が速いらすぃ)
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 */
	public static function existsFile(string $path): bool
	{
		return is_file($path);
	}

	/**
	 * ディレクトリが存在するか。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 */
	public static function existsDirectory(string $path): bool
	{
		return is_dir($path);
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

			$isDir = self::existsDirectory($path);

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
			if (self::existsDirectory($file)) {
				self::removeDirectory($file);
			} else {
				self::removeFile($file);
			}
		}
		rmdir($directoryPath);
	}

	public static function removeFile(string $filePath): bool
	{
		if (self::existsFile($filePath)) {
			return unlink($filePath);
		}

		return false;
	}

	/**
	 * ディレクトリを破棄・作成する
	 *
	 * @param string $directoryPath 対象ディレクトリ。
	 * @return void
	 */
	public static function cleanupDirectory(string $directoryPath, int $permissions = self::DIRECTORY_PERMISSIONS): void
	{
		if (self::existsDirectory($directoryPath)) {
			self::removeDirectory($directoryPath);
		}
		self::createDirectory($directoryPath, $permissions);
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
