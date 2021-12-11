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
}
