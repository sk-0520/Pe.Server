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
}
