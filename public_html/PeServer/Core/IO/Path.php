<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * パス処理系。
 */
abstract class Path
{
	/**
	 * パスの正規化。
	 *
	 * @param string $path パス。
	 * @return string 絶対パス。
	 */
	public static function normalize(string $path): string
	{
		$targetPath = Text::replace($path, ['/', '\\'], DIRECTORY_SEPARATOR);
		$parts = array_filter(Text::split($targetPath, DIRECTORY_SEPARATOR), 'mb_strlen');
		$absolutes = [];
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

		$result = Text::join(DIRECTORY_SEPARATOR, $absolutes);
		if (Text::getByteCount($targetPath) && $targetPath[0] === DIRECTORY_SEPARATOR) {
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
	public static function combine(string $basePath, string ...$addPaths): string
	{
		$paths = array_merge([$basePath], array_map(function ($s) {
			return Text::trim($s, '/\\');
		}, $addPaths));
		$paths = array_filter($paths, function ($v, $k) {
			return !Text::isNullOrEmpty($v) && ($k === 0 ? true :  $v !== '/' && $v !== '\\');
		}, ARRAY_FILTER_USE_BOTH);


		$joinedPath = Text::join(DIRECTORY_SEPARATOR, $paths);
		return self::normalize($joinedPath);
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
	 * @param boolean $withDot `.` を付与するか。
	 * @return string
	 */
	public static function getFileExtension(string $path, bool $withDot = false): string
	{
		if (Text::isNullOrWhiteSpace($path)) {
			return DefaultValue::EMPTY_STRING;
		}

		$dotIndex = Text::getLastPosition($path, '.');
		if ($dotIndex === DefaultValue::NOT_FOUND_INDEX) {
			return DefaultValue::EMPTY_STRING;
		}

		$result = Text::substring($path, $dotIndex);
		if ($withDot) {
			return $result;
		}

		if (!Text::getByteCount($result)) {
			return DefaultValue::EMPTY_STRING;
		}

		return Text::substring($result, 1);
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
		$dotIndex = Text::getLastPosition($fileName, '.');
		if ($dotIndex === DefaultValue::NOT_FOUND_INDEX) {
			return $fileName;
		}

		return Text::substring($fileName, 0, $dotIndex);
	}

	/**
	 * パスの分割。
	 *
	 * `pathinfo` ラッパー。
	 *
	 * @param string $path
	 * @return PathParts
	 * @throws ArgumentException
	 * @see https://php.net/manual/function.pathinfo.php
	 */
	public static function toParts(string $path): PathParts
	{
		if (Text::isNullOrWhiteSpace($path)) {
			throw new ArgumentException('$path');
		}

		$parts = pathinfo($path);

		$result = new PathParts(
			ArrayUtility::getOr($parts, 'dirname', '.'),
			ArrayUtility::getOr($parts, 'basename', DefaultValue::EMPTY_STRING),
			ArrayUtility::getOr($parts, 'filename', DefaultValue::EMPTY_STRING),
			ArrayUtility::getOr($parts, 'extension', DefaultValue::EMPTY_STRING)
		);

		return $result;
	}
}
