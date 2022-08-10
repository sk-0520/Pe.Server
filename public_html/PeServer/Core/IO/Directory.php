<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use \stdClass;
use PeServer\Core\Binary;
use PeServer\Core\Cryptography;
use PeServer\Core\DefaultValue;
use PeServer\Core\Encoding;
use PeServer\Core\Environment;
use PeServer\Core\ErrorHandler;
use PeServer\Core\IO\IOState;
use PeServer\Core\IO\Stream;
use PeServer\Core\Serialization\Json;
use PeServer\Core\ResultData;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;

/**
 * ファイル(+ディレクトリ)処理系。
 */
abstract class Directory
{
	/** ディレクトリ作成時の通常権限。 */
	public const DIRECTORY_PERMISSIONS = 0755;

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
	 * ディレクトリが存在するか。
	 *
	 * `is_dir` ラッパー。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 * @see https://www.php.net/manual/function.is-dir.php
	 */
	public static function exists(string $path): bool
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

			$path = Path::combine($directoryPath, $item);

			$isDir = self::exists($path);

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
		$pattern = Path::combine($directoryPath, $wildcard);
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
				if (self::exists($file)) {
					if (!self::removeDirectory($file, $recursive)) {
						return false;
					}
				} else {
					if (!File::removeFile($file)) {
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
	 * ディレクトリを破棄・作成する
	 *
	 * @param string $directoryPath 対象ディレクトリ。
	 * @return void
	 */
	public static function cleanupDirectory(string $directoryPath, int $permissions = self::DIRECTORY_PERMISSIONS): void
	{
		if (self::exists($directoryPath)) {
			self::removeDirectory($directoryPath);
		}
		self::createDirectory($directoryPath, $permissions);
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
}
