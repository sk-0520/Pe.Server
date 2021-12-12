<?php

declare(strict_types=1);

namespace PeServer\Core;

class StringUtility
{
	/**
	 * 文字列がnullか空か
	 *
	 * @param string|null $s
	 * @return boolean
	 */
	public static function isNullOrEmpty(?string $s): bool
	{
		if (is_null($s)) {
			return true;
		}

		if ($s === '0') {
			return false;
		}

		return empty($s);
	}

	/**
	 * 文字列がnullかホワイトスペースのみで構築されているか
	 *
	 * @param string|null $s
	 * @return boolean
	 */
	public static function isNullOrWhiteSpace(?string $s): bool
	{
		if (self::isNullOrEmpty($s)) {
			return true;
		}

		return strlen(trim($s)) === 0;
	}

	/**
	 * プレースホルダー文字列置き換え処理
	 *
	 * @param string $source 元文字列
	 * @param array<string,string> $map 置き換え対象辞書
	 * @param string $head
	 * @param string $tail
	 * @return string 置き換え後文字列
	 */
	public static function replaceMap(string $source, array $map, string $head = '{', string $tail = '}'): string
	{
		$escHead = preg_quote($head);
		$escTail = preg_quote($tail);
		$pattern = "/$escHead(.+?)$escTail/";

		return preg_replace_callback(
			$pattern,
			function ($matches) use ($map) {
				if (isset($map[$matches[1]])) {
					return $map[$matches[1]];
				}
				return '';
			},
			$source
		);
	}

	public static function startsWith(string $haystack, string $needle, bool $ignoreCase): bool
	{
		//PHP8
		//str_starts_with($haystack, $needle);
		if (self::isNullOrEmpty($needle)) {
			return true;
		}
		if (strlen($haystack) < strlen($needle)) {
			return false;
		}

		$word = mb_substr($haystack, 0, mb_strlen($needle));

		if ($ignoreCase) {
			return !strcasecmp($needle, $word);
		}
		return $needle === $word;
	}

	public static function endsWith(string $haystack, string $needle, bool $ignoreCase): bool
	{
		//PHP8
		//str_ends_with($haystack, $needle);
		if (self::isNullOrEmpty($needle)) {
			return true;
		}
		if (strlen($haystack) < strlen($needle)) {
			return false;
		}

		$word = mb_substr($haystack, mb_strlen($haystack) - mb_strlen($needle));

		if ($ignoreCase) {
			return !strcasecmp($needle, $word);
		}
		return $needle === $word;
	}
}
