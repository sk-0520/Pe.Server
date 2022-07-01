<?php

declare(strict_types=1);

namespace PeServer\Core;

use \stdClass;
use PeServer\Core\Binary;
use PeServer\Core\InitialValue;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\FileNotFoundException;

/**
 * パス処理系。
 */
abstract class PathUtility
{
	/**
	 * 絶対パスへ変換。
	 *
	 * @param string $path パス。
	 * @return string 絶対パス。
	 */
	public static function toCanonicalize(string $path): string
	{
		$targetPath = StringUtility::replace($path, ['/', '\\'], DIRECTORY_SEPARATOR);
		$parts = array_filter(StringUtility::split($targetPath, DIRECTORY_SEPARATOR), 'mb_strlen');
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

		$result = StringUtility::join($absolutes, DIRECTORY_SEPARATOR);
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
			return StringUtility::trim($s, '/\\');
		}, $addPaths));
		$paths = array_filter($paths, function ($v, $k) {
			return !StringUtility::isNullOrEmpty($v) && ($k === 0 ? true :  $v !== '/' && $v !== '\\');
		}, ARRAY_FILTER_USE_BOTH);


		$joinedPath = StringUtility::join($paths, DIRECTORY_SEPARATOR);
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
			return InitialValue::EMPTY_STRING;
		}

		$dotIndex = StringUtility::getLastPosition($path, '.');
		if ($dotIndex === -1) {
			return InitialValue::EMPTY_STRING;
		}

		$result = StringUtility::substring($path, $dotIndex);
		if ($withDot) {
			return $result;
		}

		if (!StringUtility::getByteCount($result)) {
			return InitialValue::EMPTY_STRING;
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
}
