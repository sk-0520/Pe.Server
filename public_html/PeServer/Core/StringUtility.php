<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\Throws\ArgumentException;

abstract class StringUtility
{
	public const TRIM_CHARACTERS = " \n\r\t\v\0";

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
	 * 文字列長を取得。
	 *
	 * @param string $value
	 * @return integer 文字数。
	 */
	public static function getLength(string $value): int
	{
		return mb_strlen($value);
	}

	/**
	 * 文字列バイト数を取得。
	 *
	 * @param string $value 対象文字列。
	 * @return integer バイト数。
	 */
	public static function getByteCount(string $value): int
	{
		return strlen($value);
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


	/**
	 * 文字列位置を取得。
	 *
	 * @param string $haystack 対象文字列。
	 * @param string $needle 検索文字列。
	 * @param integer $offset 開始文字数目。負数の場合は後ろから。
	 * @return integer 見つかった文字位置。見つかんない場合は -1
	 */
	public static function getPosition(string $haystack, string $needle, int $offset = 0): int
	{
		$result =  mb_strpos($haystack, $needle, $offset);
		if ($result === false) {
			return -1;
		}

		return $result;
	}

	/**
	 * 先頭文字列一致判定。
	 *
	 * @param string $haystack 対象文字列。
	 * @param string $needle 検索文字列。
	 * @param boolean $ignoreCase 大文字小文字を区別するか。
	 * @return boolean
	 */
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

	/**
	 * 終端文字列一致判定。
	 *
	 * @param string $haystack 対象文字列。
	 * @param string $needle 検索文字列。
	 * @param boolean $ignoreCase 大文字小文字を区別するか。
	 * @return boolean
	 */
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

		$word = mb_substr($haystack, -mb_strlen($needle));

		if ($ignoreCase) {
			return !strcasecmp($needle, $word);
		}
		return $needle === $word;
	}

	/**
	 * 文字列を含んでいるか判定。
	 *
	 * @param string $haystack 対象文字列。
	 * @param string $needle 検索文字列。
	 * @param boolean $ignoreCase 大文字小文字を区別するか。
	 * @return boolean
	 */
	public static function contains(string $haystack, string $needle, bool $ignoreCase): bool
	{
		//PHP8
		//str_contains
		if (self::isNullOrEmpty($needle)) {
			return true;
		}
		if (strlen($haystack) < strlen($needle)) {
			return false;
		}

		if ($ignoreCase) {
			return stripos($haystack, $needle) !== false;
		}

		return strpos($haystack, $needle) !== false;
	}

	/**
	 * 文字列部分切り出し。
	 *
	 * @param string $value 対象文字列。
	 * @param integer $offset 開始文字数目。負数の場合は後ろから。
	 * @param integer $length 抜き出す長さ。負数の場合は最後まで($offset)
	 * @return string 切り抜き後文字列。
	 */
	public static function substring(string $value, int $offset, int $length = -1): string
	{
		return mb_substr($value, $offset, 0 <= $length ? $length : null);
	}

	/**
	 * 小文字を大文字に変換。
	 *
	 * @param string $value
	 * @return string
	 */
	public static function toLower(string $value): string
	{
		return mb_strtolower($value);
	}

	/**
	 * 大文字を小文字に変換。
	 *
	 * @param string $value
	 * @return string
	 */
	public static function toUpper(string $value): string
	{
		return mb_strtoupper($value);
	}

	/**
	 * 文字列分割。
	 *
	 * @param string $value 対象文字列。
	 * @param string $separator 分割対象文字列。
	 * @param integer $limit 分割数。
	 * @return string[] 分割された文字列。
	 * @throws ArgumentException 分割失敗(PHP8未満)
	 * @throws \ValueError 分割失敗(PHP8以上)
	 * @see https://www.php.net/manual/ja/function.explode.php
	 */
	public static function split(string $value, string $separator, int $limit = PHP_INT_MAX): array
	{
		if (StringUtility::isNullOrEmpty($separator)) {
			throw new ArgumentException();
		}

		/** non-empty-string $separator */
		$result = explode($separator, $value, $limit); // @phpstan-ignore-line

		return $result;
	}

	/**
	 * 文字列結合。
	 *
	 * @param string $separator
	 * @param string[] $values
	 * @return string
	 * @see https://www.php.net/manual/ja/function.implode.php
	 */
	public static function join(string $separator, array $values): string
	{
		return implode($separator, $values);
	}

	/**
	 * トリム処理。
	 *
	 * @param string $value 対象文字列。
	 * @param string $characters トリム対象文字。
	 * @return string トリム後文字列。
	 * @see https://www.php.net/manual/ja/function.trim.php
	 */
	public static function trim(string $value, string $characters = self::TRIM_CHARACTERS): string
	{
		return \trim($value, $characters);
	}

	/**
	 * 左トリム。
	 *
	 * @param string $value 対象文字列。
	 * @param string $characters トリム対象文字。
	 * @return string トリム後文字列。
	 */
	public static function trimStart(string $value, string $characters = self::TRIM_CHARACTERS): string
	{
		return ltrim($value, $characters);
	}

	/**
	 * 右トリム。
	 *
	 * @param string $value 対象文字列。
	 * @param string $characters トリム対象文字。
	 * @return string トリム後文字列。
	 */
	public static function trimEnd(string $value, string $characters = self::TRIM_CHARACTERS): string
	{
		return rtrim($value, $characters);
	}


	/**
	 * データ出力。
	 *
	 * var_export/print_r で迷ったり $return = true 忘れのためのラッパー。
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function dump($value): string
	{
		return var_export($value, true);
	}
}
