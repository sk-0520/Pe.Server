<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ParseException;

/**
 * 数値系。
 */
abstract class Numeric
{
	private const INT_PATTERN = '/^\s*(\+|\-)?\d+\s*$/';

	public static function parseInteger(string $input): int
	{
		if (!preg_match(self::INT_PATTERN, $input)) {
			throw new ParseException($input);
		}

		return (int)trim($input);
	}

	public static function tryParseInteger(string $input, ?int &$result): bool
	{
		if (!preg_match(self::INT_PATTERN, $input)) {
			return false;
		}

		$result = (int)trim($input);
		return true;
	}
}
