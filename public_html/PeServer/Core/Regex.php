<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

abstract class Regex
{
	public static function isMatch(string $input, string $pattern): bool
	{
		$result = preg_match($pattern, $input);
		if ($result === false) {
			throw new ArgumentException();
		}

		return (bool)$result;
	}
}
