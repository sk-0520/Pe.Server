<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\OrderBy;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\RegexDelimiterException;
use PeServer\Core\Throws\RegexException;
use PeServer\Core\Throws\RegexPatternException;

/**
 * 正規表現ラッパー。
 */
class Regex
{
	#region define

	public const UNLIMITED = -1;
	private const DELIMITER_CLOSE_START_INDEX = 2;

	#endregion

	#region variable

	private static ?Encoding $firstDefaultEncoding = null;
	private readonly Encoding $encoding;

	#endregion

	/**
	 * 生成。
	 *
	 * @param Encoding|null $encoding UTF-8の場合 /pattern/u の u を追加する用。標準エンコーディング(mb_internal_encoding: 基本UTF-8)を想定。
	 */
	public function __construct(?Encoding $encoding = null)
	{
		if ($encoding === null) {
			if (self::$firstDefaultEncoding === null) {
				self::$firstDefaultEncoding = Encoding::getDefaultEncoding();
			}
			$this->encoding = self::$firstDefaultEncoding;
		} else {
			$this->encoding = $encoding;
		}
	}

	#region function

	private function normalizePattern(string $pattern): string
	{
		$byteLength = strlen($pattern);
		if ($byteLength < 3) {
			throw new RegexPatternException($pattern);
		}

		$open = $pattern[0];
		$closeIndex = match ($open) {
			'(' => strrpos($pattern, ')', self::DELIMITER_CLOSE_START_INDEX),
			'{' => strrpos($pattern, '}', self::DELIMITER_CLOSE_START_INDEX),
			'[' => strrpos($pattern, ']', self::DELIMITER_CLOSE_START_INDEX),
			'<' => strrpos($pattern, '>', self::DELIMITER_CLOSE_START_INDEX),
			default => strrpos($pattern, $open, self::DELIMITER_CLOSE_START_INDEX),
		};
		if ($closeIndex === false || $closeIndex === 0) {
			throw new RegexDelimiterException();
		}

		//UTF8対応
		if ($this->encoding->name === Encoding::ENCODE_UTF8) {
			if ($closeIndex === ($byteLength - 1)) {
				return $pattern . 'u';
			}
			if (strpos($pattern, 'u', $closeIndex) === false) {
				return $pattern . 'u';
			}
		}

		return $pattern;
	}

	/**
	 * 正規表現パターンをエスケープコードに変換。
	 *
	 * `preg_quote` ラッパー。
	 *
	 * @param string $s 正規表現パターン。
	 * @param string|null $delimiter デリミタ(null: `/`)。
	 * @return string
	 * @see https://www.php.net/manual/function.preg-quote.php
	 */
	public function escape(string $s, ?string $delimiter = null): string
	{
		return preg_quote($s, $delimiter);
	}

	/**
	 * パターンにマッチするか。
	 *
	 * @param string $input 対象文字列。
	 * @param string $pattern 正規表現パターン。
	 * @phpstan-param literal-string $pattern
	 * @return boolean マッチしたか。
	 * @throws RegexException 正規表現処理失敗。
	 */
	public function isMatch(string $input, string $pattern): bool
	{
		$result = ErrorHandler::trap(fn () => preg_match($this->normalizePattern($pattern), $input));
		if ($result->isFailureOrFalse()) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return (bool)$result->value;
	}

	/**
	 * パターンマッチ。
	 *
	 * @param string $input
	 * @param string $pattern
	 * @phpstan-param literal-string $pattern
	 * @return array<int|string,string>
	 * @phpstan-return array<array-key,string>
	 */
	public function matches(string $input, string $pattern): array
	{
		$matches = [];
		$result = ErrorHandler::trap(function () use ($pattern, $input, &$matches) {
			return preg_match_all($this->normalizePattern($pattern), $input, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
		});
		if ($result->isFailureOrFalse()) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}
		if ($result->value === 0) {
			return [];
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
		$items = Arr::sortByKey($items, OrderBy::Ascending);

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
	 * @see https://www.php.net/manual/function.preg-replace.php
	 */
	public function replace(string $source, string $pattern, string $replacement, int $limit = self::UNLIMITED): string
	{
		if (!$limit) {
			throw new ArgumentException();
		}

		$result = ErrorHandler::trap(fn () => preg_replace($this->normalizePattern($pattern), $replacement, $source, $limit));
		if ($result->isFailureOrFailValue(null)) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return $result->value;
	}


	/**
	 * 正規表現置き換え。
	 *
	 * @param string $source 一致する対象を検索する文字列。
	 * @param string $pattern 一致させる正規表現パターン。
	 * @param callable $replacement 置換処理。
	 * @phpstan-param callable(array<array-key,string>):string $replacement
	 * @param int $limit 各パターンによる 置換を行う最大回数。
	 * @throws ArgumentException 引数がおかしい。
	 * @throws RegexException 正規表現処理失敗。
	 * @return string
	 * @see https://www.php.net/manual/function.preg-replace-callback.php
	 */
	public function replaceCallback(string $source, string $pattern, callable $replacement, int $limit = self::UNLIMITED): string
	{
		if (!$limit) {
			throw new ArgumentException();
		}

		$result = preg_replace_callback($this->normalizePattern($pattern), $replacement, $source, $limit);
		if ($result === null) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return $result;
	}

	/**
	 * 分割処理。
	 *
	 * @param string $source 入力文字列。
	 * @param string $pattern パターン。
	 * @return string[]
	 */
	public function split(string $source, string $pattern): array
	{
		$result = preg_split($this->normalizePattern($pattern), $source);
		if ($result === false) {
			throw new RegexException(preg_last_error_msg(), preg_last_error());
		}

		return $result;
	}

	#endregion
}
