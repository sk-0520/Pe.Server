<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use PeServer\Core\Collections\Arr;
use PeServer\Core\DefaultValue;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * URL系。
 */
abstract class UrlUtility
{
	#region define

	public const URL_KIND_RFC1738 = PHP_QUERY_RFC1738;
	public const URL_KIND_RFC3986 = PHP_QUERY_RFC3986;

	public const LOCALHOST_PATTERN = '/https?:\/\/(\w*:\\w*@)?((localhost)|(127\.0\.0\.1))\b/';

	#endregion

	#region function

	public static function convertPathToUrl(string $path, SpecialStore $specialStore): string
	{
		/** @var string */
		$httpsProtocol = $specialStore->getServer('HTTPS', Text::EMPTY);
		$httpProtocol = Text::isNullOrEmpty($httpsProtocol) ? 'http://' : 'https://';
		return $httpProtocol . $specialStore->getServer('SERVER_NAME') . '/' .  Text::trim($path, '/');
	}

	/**
	 * URLエンコード。
	 *
	 * @param string $input
	 * @param int $queryKind
	 * @phpstan-param self::URL_KIND_* $queryKind
	 * @return string
	 * @see https://www.php.net/manual/function.urldecode.php
	 * @see https://www.php.net/manual/function.rawurldecode.php
	 * @throws ArgumentException
	 */
	public static function encode(string $input, int $queryKind = self::URL_KIND_RFC1738): string
	{
		return match ($queryKind) {
			self::URL_KIND_RFC1738 => urlencode($input),
			self::URL_KIND_RFC3986 => rawurlencode($input), //@phpstan-ignore-line
			default => throw new ArgumentException('$queryKind'), //@phpstan-ignore-line
		};
	}

	/**
	 * URLデコード。
	 *
	 * @param string $input
	 * @param int $queryKind
	 * @phpstan-param self::URL_KIND_* $queryKind
	 * @return string
	 * @see https://www.php.net/manual/function.urldecode.php
	 * @see https://www.php.net/manual/function.rawurldecode.php
	 * @throws ArgumentException
	 */
	public static function decode(string $input, int $queryKind = self::URL_KIND_RFC1738): string
	{
		return match ($queryKind) {
			self::URL_KIND_RFC1738 => urldecode($input),
			self::URL_KIND_RFC3986 => rawurldecode($input), //@phpstan-ignore-line
			default => throw new ArgumentException('$queryKind'), //@phpstan-ignore-line
		};
	}

	/**
	 * クエリ生成。
	 *
	 * @param array<int|string,string> $query
	 * @phpstan-param array<int|non-empty-string,string> $query
	 * @param int $queryKind
	 * @phpstan-param self::URL_KIND_* $queryKind
	 * @return string
	 */
	public static function buildQuery(array $query, int $queryKind = self::URL_KIND_RFC1738): string
	{
		$items = [];
		foreach ($query as $key => $value) {
			if (is_int($key)) {
				if (!Text::isNullOrEmpty($value)) {
					$items[] = self::encode($value, $queryKind);
				}
			} else {
				$items[] = self::encode($key, $queryKind) . '=' . self::encode($value, $queryKind);
			}
		}

		return Text::join('&', $items);
	}

	/**
	 * URLパスとクエリを結合。
	 *
	 * @param string $baseUrl
	 * @param array<int|string,string> $query
	 * @phpstan-param array<array-key,string> $query
	 * @param int $queryKind
	 * @phpstan-param self::URL_KIND_* $queryKind
	 * @return string
	 */
	public static function joinQuery(string $baseUrl, array $query, int $queryKind = self::URL_KIND_RFC1738): string
	{
		if (!Arr::isNullOrEmpty($query)) {
			if (Text::contains($baseUrl, '?', false)) {
				return $baseUrl . '&' . self::buildQuery($query, $queryKind);
			}

			return $baseUrl . '?' . self::buildQuery($query, $queryKind);
		}

		return $baseUrl;
	}

	/**
	 * パスをURLに変換しつつクエリ結合。
	 *
	 * @param string $path
	 * @param array<int|string,string> $query
	 * @phpstan-param array<array-key,string> $query
	 * @param int $queryKind
	 * @phpstan-param self::URL_KIND_* $queryKind
	 * @return string
	 */
	public static function buildPath(string $path, array $query, SpecialStore $specialStore, int $queryKind = self::URL_KIND_RFC1738): string
	{
		$pathUrl = self::convertPathToUrl($path, $specialStore);
		return self::joinQuery($pathUrl, $query);
	}

	/**
	 * パス結合。
	 *
	 * @param string $baseUrl
	 * @param string ...$paths
	 * @return string
	 */
	public static function joinPath(string $baseUrl, string ...$paths): string
	{
		$pair = Text::split($baseUrl, '?', 2);
		$url = Text::trimEnd($pair[0], '/');

		$trimPaths = array_values(array_map(function ($i) {
			return Text::trim($i, " \t/?");
		}, $paths));

		$joinUrl = Text::join('/', [$url, ...$trimPaths]);
		if (1 < Arr::getCount($pair)) {
			$joinUrl .= '?' . $pair[1];
		}

		return $joinUrl;
	}

	/**
	 * キャッシュ考慮不要な(HTTP)パスか。
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function isIgnoreCaching(string $path): bool
	{
		$isExternal =
			Text::startsWith($path, '//', false)
			||
			Text::startsWith($path, 'https://', false)
			||
			Text::startsWith($path, 'http://', false)
			||
			Text::contains($path, '?', false);

		return $isExternal;
	}

	#endregion
}
