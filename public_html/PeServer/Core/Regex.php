<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\RegexException;

/**
 * 正規表現ラッパー。
 */
abstract class Regex
{
	/**
	 * パターンにマッチするか。
	 *
	 * @param string $input 対象文字列。
	 * @param string $pattern 正規表現パターン。
	 * @return boolean マッチしたか。
	 * @throws RegexException 正規表現処理失敗。
	 */
	public static function isMatch(string $input, string $pattern): bool
	{
		$result = preg_match($pattern, $input);
		if ($result === false) {
			throw new RegexException();
		}

		return (bool)$result;
	}

	/**
	 * 正規表現パターンをエスケープコードに変換。
	 *
	 * preg_quoteラッパー。
	 * https://www.php.net/manual/ja/function.preg-quote.php
	 *
	 * @param string $s 正規表現パターン。
	 * @param string|null $delimiter デリミタ。
	 * @return string
	 */
	public static function escape(string $s, ?string $delimiter = null): string
	{
		return preg_quote($s, $delimiter);
	}
}
