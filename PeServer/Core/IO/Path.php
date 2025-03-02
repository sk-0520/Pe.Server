<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use PeServer\Core\Collections\Arr;
use PeServer\Core\IO\PathParts;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * パス処理系。
 */
abstract class Path
{
	#region function

	/**
	 * パスの正規化。
	 *
	 * @param string $path パス。
	 * @return string 絶対パス。
	 */
	public static function normalize(string $path): string
	{
		$targetPath = Text::replace($path, ['/', '\\'], DIRECTORY_SEPARATOR);
		$parts = array_filter(Text::split($targetPath, DIRECTORY_SEPARATOR), fn($s) => (bool)mb_strlen($s));
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
	 * `dirname` ラッパー。
	 *
	 * @param string $path
	 * @return string
	 * @see https://php.net/manual/function.dirname.php
	 * @phpstan-pure
	 */
	public static function getDirectoryPath(string $path): string
	{
		return dirname($path);
	}

	/**
	 * ファイル名を取得。
	 *
	 * `basename` ラッパー。
	 *
	 * @param string $path
	 * @return string
	 * @phpstan-pure
	 * @see https://php.net/manual/function.basename.php
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
			return Text::EMPTY;
		}

		$dotIndex = Text::getLastPosition($path, '.');
		if ($dotIndex === -1) {
			return Text::EMPTY;
		}

		$result = Text::substring($path, $dotIndex);
		if ($withDot) {
			return $result;
		}

		if (!Text::getByteCount($result)) {
			return Text::EMPTY;
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
		if ($dotIndex === -1) {
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
			$parts['dirname'],
			$parts['basename'],
			$parts['filename'],
			$parts['extension'] ?? Text::EMPTY
		);

		return $result;
	}

	/**
	 * ファイルパスに対して環境名を付与する。
	 *
	 * * `file.ext` + `debug` = `file.debug.ext`
	 *
	 * @param string $path パス。
	 * @param string $environment 環境名。
	 * @return string 環境名が付与されたファイルパス。入力値によっては環境に合わせたディレクトリセパレータに変わる可能性あり(`./a.b` => `.\a.env.b`)
	 * @throws ArgumentException
	 */
	public static function setEnvironmentName(string $path, string $environment): string
	{
		if (Text::isNullOrWhiteSpace($path)) {
			throw new ArgumentException('$path');
		}
		if (Text::isNullOrWhiteSpace($environment)) {
			throw new ArgumentException('$environment');
		}

		$parts = self::toParts($path);

		$name = Text::isNullOrEmpty($parts->extension)
			? $parts->fileNameWithoutExtension . '.' . $environment
			: $parts->fileNameWithoutExtension . '.' . $environment . '.' . $parts->extension;

		if ($parts->directory === '.' && !(Text::startsWith($path, './', false) || Text::startsWith($path, '.\\', false))) {
			return $name;
		}

		if ($parts->directory === DIRECTORY_SEPARATOR) {
			return DIRECTORY_SEPARATOR . $name;
		}

		if (Text::endsWith($parts->directory, DIRECTORY_SEPARATOR, false)) {
			return Text::trimEnd($parts->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
		}

		return $parts->directory . DIRECTORY_SEPARATOR . $name;
	}

	#endregion
}
