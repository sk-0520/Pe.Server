<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

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
	 */
	public static function isMatch(string $input, string $pattern): bool
	{
		$result = preg_match($pattern, $input);
		if ($result === false) {
			throw new ArgumentException();
		}

		return (bool)$result;
	}
}
