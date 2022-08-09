<?php

declare(strict_types=1);

namespace PeServer\Core;

use \stdClass;
use Directory;
use PeServer\Core\Binary;
use PeServer\Core\DefaultValue;
use PeServer\Core\IOState;
use PeServer\Core\PathUtility;
use PeServer\Core\ResultData;
use PeServer\Core\Stream;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;

/**
 * ファイル(+ディレクトリ)処理系。
 */
abstract class IOUtility
{
	/** ディレクトリ作成時の通常権限。 */
	public const DIRECTORY_PERMISSIONS = 0755;

	/**
	 * ファイルが存在しない場合に空ファイルの作成。
	 *
	 * `touch` ラッパー。
	 *
	 * @param string $path
	 * @return bool
	 * @see https://www.php.net/manual/function.touch.php
	 */
	public static function createEmptyFileIfNotExists(string $path): bool
	{
		return touch($path);
	}

	/**
	 * ファイルサイズを取得。
	 *
	 * @param string $path
	 * @return integer
	 * @return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.filesize.php
	 * @throws IOException
	 */
	public static function getFileSize(string $path): int
	{
		/** @phpstan-var ResultData<UnsignedIntegerAlias|false> */
		$result = ErrorHandler::trapError(fn () => filesize($path));
		if (!$result->success || $result->value === false) {
			throw new IOException();
		}

		return $result->value;
	}

	public static function getState(string $path): IOState
	{
		/** @var ResultData<array<string|int,int>|false> */
		$result = ErrorHandler::trapError(fn () => stat($path));
		if (!$result->success) {
			throw new IOException();
		}
		if ($result->value === false) {
			throw new IOException();
		}

		return IOState::createFromStat($result->value);
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
		$value = $json->decode($content->getRaw());

		return $value;
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
		$value = $json->encode($data);

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
	 *
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
	 * self::existsItem より速い。
	 * `file_exists`より`is_file`の方が速いらすぃ
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
	 * @param boolean $file
	 * @param boolean $directory
	 * @param boolean $recursive 再帰的に取得するか。
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

			$path = PathUtility::combine($directoryPath, $item);

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
		$pattern = PathUtility::combine($directoryPath, $wildcard);
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
	 * ファイルが存在する場合に削除する。
	 *
	 * @param string $filePath
	 * @return bool
	 */
	public static function removeFileIfExists(string $filePath): bool
	{
		if (!self::existsItem($filePath)) {
			return false;
		}

		/** @var ResultData<bool> */
		$result = ErrorHandler::trapError(fn () => unlink($filePath));
		if (!$result->success) {
			return false;
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
	 * 一時ディレクトリ設定。
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function setTemporaryDirectory(string $path): bool
	{
		self::createDirectoryIfNotExists($path);

		Environment::setVariable('TMP', $path);
		Environment::setVariable('TMPDIR', $path);
		Environment::setVariable('TEMP', $path);

		return true;
	}

	/**
	 * 一意なファイルパスを取得。
	 *
	 * `tempnam` ラッパー。
	 *
	 * @param string $directoryPath ファイル名の親ディレクトリ。
	 * @param string $prefix プレフィックス。
	 * @return string
	 * @see https://www.php.net/manual/function.tempnam.php
	 */
	public static function createUniqueFilePath(string $directoryPath, string $prefix): string
	{
		if (StringUtility::isNullOrWhiteSpace($directoryPath)) {
			throw new ArgumentException('$directoryPath');
		}

		$result = tempnam($directoryPath, $prefix);
		if ($result === false) {
			throw new IOException();
		}

		return $result;
	}

	/**
	 * 一時ディレクトリ取得。
	 *
	 * `sys_get_temp_di` ラッパー。
	 *
	 * @return string
	 * @see https://www.php.net/manual/function.sys-get-temp-dir.php
	 */
	public static function getTemporaryDirectory(): string
	{
		return sys_get_temp_dir();
	}

	/**
	 * 一時ファイルの取得。
	 *
	 * @param string $prefix
	 * @return string
	 */
	public static function createTemporaryFilePath(string $prefix = ''): string
	{
		if (StringUtility::isNullOrWhiteSpace($prefix)) {
			$prefixLength = PHP_OS === 'Windows' ? 3 : 64;
			$prefix = Cryptography::generateRandomString($prefixLength, Cryptography::FILE_RANDOM_STRING);
		}

		return self::createUniqueFilePath(self::getTemporaryDirectory(), $prefix);
	}

	/**
	 * 一時ファイルのストリーム作成。
	 *
	 * メモリ・一時ファイル兼メモリのストリームを使用する場合は、
	 * `Stream::openMemory`, `Stream::openTemporary` を参照のこと。
	 *
	 * `tmpfile` ラッパー。
	 *
	 * @param Encoding|null $encoding
	 * @return Stream
	 * @throws IOException
	 * @see https://www.php.net/manual/function.tmpfile.php
	 */
	public static function createTemporaryFileStream(?Encoding $encoding = null): Stream
	{
		$resource = tmpfile();
		if ($resource === false) {
			throw new IOException();
		}

		return new Stream($resource, $encoding);
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
