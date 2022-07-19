<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\StringUtility;

/**
 * URL系。
 */
abstract class UrlUtility
{
	public const LOCALHOST_PATTERN = '/https?:\/\/(\w*:\\w*@)?((localhost)|(127\.0\.0\.1))\b/';

	public static function convertPathToUrl(string $path, SpecialStore $specialStore): string
	{
		/** @var string */
		$httpsProtocol = $specialStore->getServer('HTTPS', InitialValue::EMPTY_STRING);
		$httpProtocol = StringUtility::isNullOrEmpty($httpsProtocol) ? 'http://' : 'https://';
		return $httpProtocol . $specialStore->getServer('SERVER_NAME') . '/' .  StringUtility::trim($path, '/');
	}

	/**
	 * URLパスとクエリを結合。
	 *
	 * @param string $baseUrl
	 * @param array<string,string> $query
	 * @return string
	 */
	public static function joinQuery(string $baseUrl, array $query): string
	{
		if (!ArrayUtility::isNullOrEmpty($query)) {
			if (StringUtility::contains($baseUrl, '?', false)) {
				return $baseUrl . '&' . http_build_query($query);
			}

			return $baseUrl . '?' . http_build_query($query);
		}

		return $baseUrl;
	}

	/**
	 * パスをURLに変換しつつクエリ結合。
	 *
	 * @param string $path
	 * @param array<non-empty-string,string> $query
	 * @return string
	 */
	public static function buildPath(string $path, array $query, SpecialStore $specialStore): string
	{
		$pathUrl = self::convertPathToUrl($path, $specialStore);
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
