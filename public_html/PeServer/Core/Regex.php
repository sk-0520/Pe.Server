<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\RegexException;

/**
 * 正規表現ラッパー。
 */
abstract class Regex
{
	public const UNLIMITED = -1;

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
		$result = ErrorHandler::trapError(fn () => preg_match($pattern, $input));
		if (!$result->success) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}
		if($result->value === false) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return (bool)$result->value;
	}

	/**
	 * パターンマッチ。
	 *
	 * @param string $input
	 * @param string $pattern
	 * @return array<int|string,string>
	 */
	public static function matches(string $input, string $pattern): array
	{
		$matches = [];
		$result = ErrorHandler::trapError(function () use ($pattern, $input, &$matches) {
			return preg_match_all($pattern, $input, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
		});
		if (!$result->success) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}
		if($result->value === false) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		// 最初のやつは無かったことにする
		array_shift($matches);

		$items = [];
		foreach ($matches as $key => $match) {
			if (is_int($key)) {
				foreach ($match as $v) {
					$items[$v[1]] = $v[0];
				}
			} else {
				$items[$key] = $match[0][0];
			}
		}
		ksort($items);

		$resultMatches = [
			0 => $input,
		];

		foreach ($items as $key => $item) {
			if (is_int($key)) {
				$resultMatches[] = $item;
			} else {
				$resultMatches[$key] = $item;
			}
		}

		return $resultMatches;
	}

	/**
	 * 正規表現置き換え。
	 *
	 * @param string $source 一致する対象を検索する文字列。
	 * @param string $pattern 一致させる正規表現パターン。
	 * @param string $replacement 置換文字列。
	 * @param int $limit 各パターンによる 置換を行う最大回数。
	 * @throws ArgumentException 引数がおかしい。
	 * @throws RegexException 正規表現処理失敗。
	 * @return string
	 */
	public static function replace(string $source, string $pattern, string $replacement, int $limit = self::UNLIMITED): string
	{
		if (!$limit) {
			throw new ArgumentException();
		}

		$result = preg_replace($pattern, $replacement, $source, $limit);
		if ($result === null) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return $result;
	}


	/**
	 * 正規表現置き換え。
	 *
	 * @param string $source 一致する対象を検索する文字列。
	 * @param string $pattern 一致させる正規表現パターン。
	 * @param callable(array<int,string>|array<string,string>):string $replacement 置換処理。
	 * @param int $limit 各パターンによる 置換を行う最大回数。
	 * @throws ArgumentException 引数がおかしい。
	 * @throws RegexException 正規表現処理失敗。
	 * @return string
	 */
	public static function replaceCallback(string $source, string $pattern, callable $replacement, int $limit = self::UNLIMITED): string
	{
		if (!$limit) {
			throw new ArgumentException();
		}

		$result = preg_replace_callback($pattern, $replacement, $source, $limit);
		if ($result === null) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return $result;
	}
}
