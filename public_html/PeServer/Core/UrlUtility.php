<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\StringUtility;


abstract class UrlUtility
{
	public const LOCALHOST_PATTERN = '/https?:\/\/(\w*:\\w*@)?((localhost)|(127\.0\.0\.1))\b/';

	public static function convertPathToUrl(string $path): string
	{
		/** @var string */
		$httpsProtocol = ArrayUtility::getOr($_SERVER, 'HTTPS', InitialValue::EMPTY_STRING);
		$httpProtocol = StringUtility::isNullOrEmpty($httpsProtocol) ? 'http://' : 'https://';
		return $httpProtocol . $_SERVER['SERVER_NAME'] . '/' .  StringUtility::trim($path, '/');
	}

	/**
	 * URLパスとクエリを結合。
	 *
	 * @param string $baseurl
	 * @param array<string,string> $query
	 * @return string
	 */
	public static function joinQuery(string $baseurl, array $query): string
	{
		if (!ArrayUtility::isNullOrEmpty($query)) {
			if (StringUtility::contains($baseurl, '?', false)) {
				return $baseurl . '&' . http_build_query($query);
			}

			return $baseurl . '?' . http_build_query($query);
		}

		return $baseurl;
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

		$trimPaths = array_values(array_map(function ($i) {
			return StringUtility::trim($i, " \t/?");
		}, $paths));

		$joinUrl = StringUtility::join([$url, ...$trimPaths], '/');
		if (1 < ArrayUtility::getCount($pair)) {
			$joinUrl .= '?' . $pair[1];
		}

		return $joinUrl;
	}
}
