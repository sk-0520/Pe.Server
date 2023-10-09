<?php

declare(strict_types=1);

namespace PeServer\Core;

use Throwable;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\Enforce;
use PeServer\Core\Throws\RegexException;
use PeServer\Core\Throws\StringException;
use PeServer\Core\Throws\Throws;

/**
 * 文字列操作。
 */
abstract class Text
{
	#region define

	/** トリム対象文字一覧。 */
	public const TRIM_CHARACTERS = " \n\r\t\v\0　";
	/** PHP の使ってる対象文字一覧 */
	public const TRIM_PHP_CHARACTERS = " \t\n\r\0\x0B";
	private const TRIM_TARGET_START = -1;
	private const TRIM_TARGET_END = 1;
	private const TRIM_TARGET_BOTH = 0;

	/** 空文字列。 */
	public const EMPTY = '';

	#endregion

	#region function

	/**
	 * 文字列が `null` か空か
	 *
	 * @param string|null $s 対象文字列。
	 * @return bool 真: `null`か空。
	 * @phpstan-assert-if-false non-empty-string $s
	 */
	public static function isNullOrEmpty(?string $s): bool
	{
		if ($s === null) {
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
	 * `TRIM_CHARACTERS` がホワイトスペースとして扱われる。
	 *
	 * @param string|null $s 対象文字列。
	 * @return bool 真: nullかホワイトスペースのみ。
	 * @phpstan-assert-if-false non-empty-string $s
	 */
	public static function isNullOrWhiteSpace(?string $s): bool
	{
		if (self::isNullOrEmpty($s)) {
			return true;
		}

		return strlen(self::trim($s)) === 0;
	}

	/**
	 * 文字列が `null` か空の場合に代替文字列を返す。
	 *
	 * @param string|null $s
	 * @param string $fallback
	 * @return string
	 */
	public static function requireNotNullOrEmpty(?string $s, string $fallback): string
	{
		if (self::isNullOrEmpty($s)) {
			return $fallback;
		}

		return $s;
	}

	/**
	 * 文字列がnullかホワイトスペースのみで構築されている場合に代替文字列を返す。
	 *
	 * @param string|null $s
	 * @param string $fallback
	 * @return string
	 */
	public static function requireNotNullOrWhiteSpace(?string $s, string $fallback): string
	{
		if (self::isNullOrWhiteSpace($s)) {
			return $fallback;
		}

		return $s;
	}

	/**
	 * 文字列長を取得。
	 *
	 * `mb_strlen` ラッパー。
	 *
	 * @param string $value 対象文字列。
	 * @return integer 文字数。
	 * @phpstan-return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.mb-strlen.php
	 */
	public static function getLength(string $value): int
	{
		return mb_strlen($value);
	}

	/*
	public static function getCharacterLength(string $value): int
	{
		$length = self::getLength($value);
		if($length < 2) {
			return $length;
		}
		return \grapheme_strlen($value);
	}
	*/

	/**
	 * 文字列バイト数を取得。
	 *
	 * `strlen` ラッパー。
	 *
	 * @param string $value 対象文字列。
	 * @return integer バイト数。
	 * @phpstan-return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.strlen.php
	 */
	public static function getByteCount(string $value): int
	{
		return strlen($value);
	}

	private static function fromCodePointCore(int $value): string
	{
		$single = mb_chr($value);
		if ($single === false) { //@phpstan-ignore-line [PHP_VERSION]
			throw new ArgumentException();
		}

		return $single;
	}

	/**
	 * Unicode のコードポイントに対応する文字を返す。
	 *
	 * `mb_chr` ラッパー。
	 *
	 * @param int|int[] $value
	 * @phpstan-param UnsignedIntegerAlias|UnsignedIntegerAlias[] $value
	 * @return string
	 * @see https://www.php.net/manual/function.mb-chr.php
	 * @throws ArgumentException
	 */
	public static function fromCodePoint(int|array $value): string
	{
		if (is_int($value)) {
			return self::fromCodePointCore($value);
		}

		$result = '';
		foreach ($value as $cp) {
			if (!is_int($cp)) { //@phpstan-ignore-line [DOCTYPE]
				throw new ArgumentException();
			}

			$result .= self::fromCodePointCore($cp);
		}

		return $result;
	}

	/**
	 * プレースホルダー文字列置き換え処理
	 *
	 * @param string $source 元文字列
	 * @phpstan-param literal-string $source
	 * @param array<string,string> $map 置き換え対象辞書
	 * @param string $head プレースホルダー先頭
	 * @phpstan-param non-empty-string $head
	 * @param string $tail プレースホルダー終端
	 * @phpstan-param non-empty-string $tail
	 * @return string 置き換え後文字列
	 * @throws StringException なんかもうあかんかった
	 */
	public static function replaceMap(string $source, array $map, string $head = '{', string $tail = '}'): string
	{
		Enforce::throwIfNullOrEmpty($head, Text::EMPTY, StringException::class);
		Enforce::throwIfNullOrEmpty($tail, Text::EMPTY, StringException::class);

		$regex = new Regex();
		$escHead = $regex->escape($head);
		$escTail = $regex->escape($tail);
		$pattern = "/$escHead(.+?)$escTail/";

		try {
			$result = $regex->replaceCallback(
				$source,
				$pattern,
				function ($matches) use ($map) {
					if (isset($map[$matches[1]])) {
						return $map[$matches[1]];
					}
					return Text::EMPTY;
				}
			);

			return $result;
		} catch (Throwable $ex) {
			Throws::reThrow(StringException::class, $ex);
		}
	}

	/**
	 * `sprintf` ラッパー。
	 *
	 * 単純な文字列置き換えであれば `Text::replaceMap` を使用する。
	 *
	 * @param string $format
	 * @param mixed ...$values
	 * @phpstan-param FormatAlias ...$values
	 * @return string
	 * @see https://www.php.net/manual/function.sprintf.php
	 * @see Text::replaceMap()
	 */
	public static function format(string $format, string|int|float ...$values): string
	{
		return sprintf($format, ...$values);
	}

	/**
	 * 数字を千の位毎にグループ化してフォーマット
	 *
	 * @param int|float $number フォーマットする数値
	 * @param int $decimals 小数点以下の桁数。 0 を指定すると、 戻り値の $decimalSeparator は省略されます
	 * @param string|null $decimalSeparator 小数点を表す区切り文字
	 * @param string|null $thousandsSeparator 千の位毎の区切り文字
	 * @return string 置き換え後文字列
	 * @see https://www.php.net/manual/function.number-format.php
	 */
	public static function formatNumber(int|float $number, int $decimals = 0, ?string $decimalSeparator = '.', ?string $thousandsSeparator = ','): string
	{
		return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
	}

	/**
	 * 文字列位置を取得。
	 *
	 * @param string $haystack 対象文字列。
	 * @param string $needle 検索文字列。
	 * @param integer $offset 開始文字数目。
	 * @phpstan-param UnsignedIntegerAlias $offset
	 * @return integer 見つかった文字位置。見つかんない場合は `-1`
	 * @phpstan-return UnsignedIntegerAlias|-1
	 * @throws ArgumentException
	 */
	public static function getPosition(string $haystack, string $needle, int $offset = 0): int
	{
		if ($offset < 0) { //@phpstan-ignore-line UnsignedIntegerAlias
			throw new ArgumentException('$offset');
		}

		$result = mb_strpos($haystack, $needle, $offset);
		if ($result === false) {
			return -1;
		}

		return $result;
	}

	/**
	 * 文字列位置を取得。
	 *
	 * @param string $haystack 対象文字列。
	 * @param string $needle 検索文字列。
	 * @param integer $offset 終端文字数目。
	 * @phpstan-param UnsignedIntegerAlias $offset
	 * @return integer 見つかった文字位置。見つかんない場合は `-1`
	 * @phpstan-return UnsignedIntegerAlias|-1
	 * @throws ArgumentException
	 */
	public static function getLastPosition(string $haystack, string $needle, int $offset = 0): int
	{
		if ($offset < 0) { //@phpstan-ignore-line [PHPDOC]
			throw new ArgumentException('$offset');
		}

		$result = mb_strrpos($haystack, $needle, $offset);
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
	 * @param boolean $ignoreCase 大文字小文字を無視するか。
	 * @return boolean
	 */
	public static function startsWith(string $haystack, string $needle, bool $ignoreCase): bool
	{
		if (!$ignoreCase && function_exists('str_starts_with')) {
			return str_starts_with($haystack, $needle);
		}

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
	 * @param boolean $ignoreCase 大文字小文字を無視するか。
	 * @return boolean
	 */
	public static function endsWith(string $haystack, string $needle, bool $ignoreCase): bool
	{
		if (!$ignoreCase && function_exists('str_ends_with')) {
			return str_ends_with($haystack, $needle);
		}

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
	 * @param boolean $ignoreCase 大文字小文字を無視するか。
	 * @return boolean
	 */
	public static function contains(string $haystack, string $needle, bool $ignoreCase): bool
	{
		if (!$ignoreCase && function_exists('str_contains')) {
			return str_contains($haystack, $needle);
		}

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
	 * 大文字を小文字に変換。
	 *
	 * @param string $value
	 * @return string
	 */
	public static function toLower(string $value): string
	{
		return mb_strtolower($value);
	}

	/**
	 * 小文字を大文字に変換。
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
	 * `explode` ラッパー。
	 *
	 * @param string $value 対象文字列。
	 * @param string $separator 分割対象文字列。
	 * @phpstan-param non-empty-string $separator 分割対象文字列。
	 * @param integer $limit 分割数。
	 * @return string[] 分割された文字列。
	 * @throws ArgumentException 分割失敗(PHP8未満)
	 * @throws \ValueError 分割失敗(PHP8以上)
	 * @see https://www.php.net/manual/function.explode.php
	 */
	public static function split(string $value, string $separator, int $limit = PHP_INT_MAX): array
	{
		if (Text::isNullOrEmpty($separator)) { //@phpstan-ignore-line [PHPDOC]
			throw new ArgumentException();
		}

		$result = explode($separator, $value, $limit);

		return $result;
	}

	/**
	 * 文字列を改行で分割。
	 *
	 * @param string $value
	 * @return string[]
	 */
	public static function splitLines(string $value): array
	{
		$lfValue = self::replace($value, ["\r\n", "\r"], "\n");
		return self::split($lfValue, "\n");
	}

	/**
	 * 文字列結合。
	 *
	 * `implode` ラッパー。
	 *
	 * @param string $separator
	 * @param string[] $values
	 * @return string
	 * @see https://www.php.net/manual/function.implode.php
	 */
	public static function join(string $separator, array $values): string
	{
		return implode($separator, $values);
	}

	/**
	 * トリム用パターンの生成。
	 *
	 * @param Regex $regex
	 * @param string $characters
	 * @param int $trimTarget
	 * @phpstan-param self::TRIM_TARGET_* $trimTarget
	 * @return string
	 */
	private static function createTrimPattern(Regex $regex, string $characters, int $trimTarget): string
	{
		$escapedCharactersPattern = $regex->escape($characters);
		$rangePattern = self::replace($escapedCharactersPattern, '\.\.', '-');

		return match ($trimTarget) {
			self::TRIM_TARGET_START => "/\A[$rangePattern]++|/",
			self::TRIM_TARGET_END => "/[$rangePattern]++\z/",
			self::TRIM_TARGET_BOTH => "/\A[$rangePattern]++|[$rangePattern]++\z/",
		};
	}

	/**
	 * トリム処理。
	 *
	 * `trim` ラッパー。
	 *
	 * @param string $value 対象文字列。
	 * @param string $characters トリム対象文字。
	 * @return string トリム後文字列。
	 * @see https://www.php.net/manual/function.trim.php
	 */
	public static function trim(string $value, string $characters = self::TRIM_CHARACTERS): string
	{
		// 1byte文字構成であればそのまま
		if (strlen($characters) == self::getLength($characters)) {
			return \trim($value, $characters);
		}

		$regex = new Regex();
		$pattern = self::createTrimPattern($regex, $characters, self::TRIM_TARGET_BOTH);
		$result = $regex->replace($value, $pattern, '');

		return $result;
	}

	/**
	 * 先頭トリム。
	 *
	 * `ltrim` ラッパー。
	 *
	 * @param string $value 対象文字列。
	 * @param string $characters トリム対象文字。
	 * @return string トリム後文字列。
	 * @see https://www.php.net/manual/function.ltrim.php
	 */
	public static function trimStart(string $value, string $characters = self::TRIM_CHARACTERS): string
	{
		// 1byte文字構成であればそのまま
		if (strlen($characters) == self::getLength($characters)) {
			return ltrim($value, $characters);
		}

		$regex = new Regex();
		$pattern = self::createTrimPattern($regex, $characters, self::TRIM_TARGET_START);
		$result = $regex->replace($value, $pattern, '');

		return $result;
	}

	/**
	 * 終端トリム。
	 *
	 * `rtrim` ラッパー。
	 *
	 * @param string $value 対象文字列。
	 * @param string $characters トリム対象文字。
	 * @return string トリム後文字列。
	 * @see https://www.php.net/manual/function.rtrim.php
	 */
	public static function trimEnd(string $value, string $characters = self::TRIM_CHARACTERS): string
	{
		// 1byte文字構成であればそのまま
		if (strlen($characters) == self::getLength($characters)) {
			return rtrim($value, $characters);
		}

		$regex = new Regex();
		$pattern = self::createTrimPattern($regex, $characters, self::TRIM_TARGET_END);
		$result = $regex->replace($value, $pattern, '');

		return $result;
	}


	/**
	 * データ出力。
	 *
	 * var_export/print_r で迷ったり $return = true 忘れのためのラッパー。
	 * 色々あったけど `var_dump` に落ち着いた感。
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function dump($value): string
	{
		//return print_r($value, true);

		$val = OutputBuffer::get(fn () => var_dump($value));
		if ($val->hasNull()) {
			return $val->toBase64();
		}

		return $val->toString();
	}

	/**
	 * 文字列置き換え
	 *
	 * `str_replace` ラッパー。
	 *
	 * @param string $source 入力文字列。
	 * @param string|string[] $oldValue 元文字列(か、元文字列配列)
	 * @param string $newValue 置き換え文字列。
	 * @return string 置き換え後文字列。
	 * @see https://www.php.net/manual/function.str-replace.php
	 */
	public static function replace(string $source, string|array $oldValue, string $newValue): string
	{
		if (is_string($oldValue) && $oldValue === $newValue) {
			return $source;
		}

		return str_replace($oldValue, $newValue, $source);
	}

	/**
	 * 文字列を反復。
	 *
	 * str_repeat ラッパー。
	 *
	 * @param string $value
	 * @param integer $count
	 * @phpstan-param UnsignedIntegerAlias $count
	 * @return string
	 * @throws ArgumentException 負数。
	 * @see https://www.php.net/manual/function.str-repeat.php
	 */
	public static function repeat(string $value, int $count): string
	{
		//@phpstan-ignore-next-line [PHPDOC]
		if ($count < 0) {
			throw new ArgumentException();
		}

		return str_repeat($value, $count);
	}

	/**
	 * 文字列を文字の配列に変換。
	 *
	 * @param string $value
	 * @phpstan-param non-empty-string $value
	 * @return string[]
	 */
	public static function toCharacters(string $value): array
	{
		if (Text::isNullOrEmpty($value)) { //@phpstan-ignore-line [PHPDOC]
			throw new ArgumentException('$value = ' . $value);
		}

		$length = Text::getLength($value);
		$charactersArray = [];
		for ($i = 0; $i < $length; $i++) {
			$c = self::substring($value, $i, 1);
			$charactersArray[] = $c;
		}

		return $charactersArray;
	}

	/**
	 * 何かしらを文字列変換
	 *
	 * @param mixed $raw
	 * @param string $newline 配列の場合の改行(未指定時に `PHP_EOL`)
	 * @return string
	 */
	public static function toString(mixed $raw, string $newline = PHP_EOL): string
	{
		if (is_string($raw)) {
			return $raw;
		}

		if (is_array($raw)) {
			return self::join($newline, $raw);
		}

		return strval($raw);
	}

	#endregion
}
