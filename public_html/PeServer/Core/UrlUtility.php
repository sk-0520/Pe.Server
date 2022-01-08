<?php

declare(strict_types=1);

namespace PeServer\Core;


abstract class UrlUtility
{
	public const LOCALHOST_PATTERN = '/https?:\/\/(\w*:\\w*@)?((localhost)|(127\.0\.0\.1))\b/';

	public static function convertPathToUrl(string $path): string
	{
		$httpProtocol = StringUtility::isNullOrEmpty(ArrayUtility::getOr($_SERVER, 'HTTPS', '')) ? 'http://' : 'https://';
		return $httpProtocol . $_SERVER['SERVER_NAME'] . '/' .  StringUtility::trim($path, '/');
	}

	/**
	 * URLパスとクエリを結合。
	 *
	 * @param string $pathUrl
	 * @param array<string,string> $query
	 * @return string
	 */
	public static function joinQuery(string $pathUrl, array $query): string
	{
		$url = StringUtility::trimEnd($pathUrl, '?');
		if (ArrayUtility::getCount($query)) {
			return $url . '?' . http_build_query($query);
		}

		return $url;
	}

	/**
	 * パスをURLに変換しつつクエリ結合。
	 *
	 * @param string $path
	 * @param array<string,string> $query
	 * @return string
	 */
	public static function buildPath(string $path, array $query): string
	{
		$pathUrl = self::convertPathToUrl($path);
		return self::joinQuery($pathUrl, $query);
	}

	public static function joinPath(string $baseUrl, string ...$paths): string
	{
		$pair = StringUtility::split($baseUrl, '?', 2);
		$url = StringUtility::trimEnd($pair[0], '/');

		$trimPaths = array_map(function ($i) {
			return StringUtility::trim($i, " \t/?");
		}, $paths);

		$joinUrl = StringUtility::join([$url, ...$trimPaths], '/');
		if (1 < ArrayUtility::getCount($pair)) {
			$joinUrl .= '?' . $pair[1];
		}

		return $joinUrl;
	}
}
