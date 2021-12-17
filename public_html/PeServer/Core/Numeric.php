<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\Throws\ParseException;

/**
 * 数値系。
 */
abstract class Numeric
{
	private const INT_PATTERN = '/^\s*(\+|\-)?\d+\s*$/';

	/**
	 * 文字列を整数に変換。
	 *
	 * @param string $input 文字列。
	 * @return integer 変換後整数。
	 * @throws ParseException 変換できない文字列。
	 */
	public static function parseInteger(string $input): int
	{
		if (!preg_match(self::INT_PATTERN, $input)) {
			throw new ParseException($input);
		}

		return (int)trim($input);
	}

	/**
	 * 文字列を整数に変換した結果を取得。
	 *
	 * @param string $input 文字列。
	 * @param integer|null $result 変換成功時の整数。
	 * @return boolean 変換成功状態。
	 */
	public static function tryParseInteger(string $input, ?int &$result): bool
	{
		if (!preg_match(self::INT_PATTERN, $input)) {
			return false;
		}

		$result = (int)trim($input);
		return true;
	}
}
