<?php

declare(strict_types=1);

namespace PeServer\Core;

use \stdClass;
use PeServer\Core\Binary;
use PeServer\Core\PathUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\FileNotFoundException;

/**
 * ファイル(+ディレクトリ)処理系。
 */
abstract class IOUtility
{
	/** ディレクトリ作成時の通常権限。 */
	public const DIRECTORY_PERMISSIONS = 0755;

	/**
	 * ファイルサイズを取得。
	 *
	 * @param string $path
	 * @return integer
	 * @phpstan-return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.filesize.php
	 * @throws IOException
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
	 * ファイルの内容を取得。
	 *
	 * @param string $path
	 * @return Binary
	 * @see https://www.php.net/manual/function.file-get-contents.php
	 * @throws IOException
	 */
	public static function readContent(string $path): Binary
	{
		$content = file_get_contents($path);
		if ($content === false) {
			throw new IOException($path);
		}

		return new Binary($content);
	}

	/**
	 * 対象ファイルに指定データを書き込み。
	 *
	 * @param string $path
	 * @param mixed $data
	 * @param boolean $append
	 * @return void
	 * @throws IOException
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
	 * @throws IOException
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
	 * @throws IOException
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
	 * @param Json|null $json JSON処理
	 * @throws IOException
	 * @throws ParseException パース失敗。
	 */
	public static function readJsonFile(string $path, Json $json = null): array
	{
		$content = self::readContent($path);

		$json ??= new Json();

		$json = $json->decode($content->getRaw());

		return $json;
	}

	/**
	 * JSONファイルとして出力。
	 *
	 * @param string $path
	 * @param array<mixed>|\stdClass $data
	 * @param Json|null $json JSON処理
	 * @return void
	 * @throws IOException
	 * @throws ParseException
	 */
	public static function writeJsonFile(string $path, array|stdClass $data, ?Json $json = null): void
	{
		$json ??= new Json();
		$value = $json->encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		self::saveContent($path, $value, false);
	}

	/**
	 * ディレクトリ作成する。
	 *
	 * ディレクトリは再帰的に作成される。
	 *
	 * `mkdir` ラッパー。
	 *
	 * @param string $directoryPath ディレクトリパス。
	 * @param int $permissions
	 * @return bool
	 * @see https://www.php.net/manual/function.mkdir.php
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
			return self::createDirectory($directoryPath, $permissions);
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
	 * `file_exists` ラッパー。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 * @see https://www.php.net/manual/function.file-exists.php
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
	 * `is_file` ラッパー。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 * @see https://www.php.net/manual/function.is-file.php
	 */
	public static function existsFile(string $path): bool
	{
		return is_file($path);
	}

	/**
	 * ディレクトリが存在するか。
	 *
	 * `is_dir` ラッパー。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 * @see https://www.php.net/manual/function.is-dir.php
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

			$path = PathUtility::joinPath($directoryPath, $item);

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
	 * パターンに一致するファイル・ディレクトリ一覧取得。
	 *
	 * `glob` ラッパー。
	 *
	 * @param string $directoryPath ディレクトリパス
	 * @param string $wildcard ワイルドカード。
	 * @return string[] 一覧。
	 * @throws IOException
	 * @see https://www.php.net/manual/function.glob.php
	 */
	public static function find(string $directoryPath, string $wildcard): array
	{
		$pattern = PathUtility::joinPath($directoryPath, $wildcard);
		$items = glob($pattern, GLOB_MARK);
		if ($items === false) {
			throw new IOException();
		}

		return $items;
	}

	/**
	 * ディレクトリを削除する。
	 * ファイル・ディレクトリはすべて破棄される。
	 *
	 * @param string $directoryPath 削除ディレクトリ。
	 * @param bool $recursive 再帰的削除を行うか。
	 * @return bool
	 * @throws IOException
	 */
	public static function removeDirectory(string $directoryPath, bool $recursive = false): bool
	{
		if ($recursive) {
			$files = self::getChildren($directoryPath, false);
			foreach ($files as $file) {
				if (self::existsDirectory($file)) {
					if (!self::removeDirectory($file, $recursive)) {
						return false;
					}
				} else {
					if (!self::removeFile($file)) {
						return false;
					}
				}
			}
		}

		/** @var ResultData<bool> */
		$result = ErrorHandler::trapError(fn () => rmdir($directoryPath));
		if (!$result->success) {
			throw new IOException();
		}

		return $result->value;
	}

	/**
	 * ファイル削除。
	 *
	 * @param string $filePath ファイルパス。
	 * @return boolean
	 * @throws IOException
	 */
	public static function removeFile(string $filePath): bool
	{
		/** @var ResultData<bool> */
		$result = ErrorHandler::trapError(fn () => unlink($filePath));
		if (!$result->success) {
			throw new IOException();
		}

		return $result->value;
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
	 * ファイルのステータスのキャッシュをクリア
	 *
	 * `clearstatcache` ラッパー。
	 *
	 * @param string|null $path
	 * @return void
	 * @see https://www.php.net/manual/function.clearstatcache.php
	 */
	public static function clearCache(?string $path)
	{
		if (is_null($path)) {
			clearstatcache(true);
			return;
		}

		if (StringUtility::isNullOrWhiteSpace($path)) {
			throw new IOException();
		}

		clearstatcache(true, $path);
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
