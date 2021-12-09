<?php

declare(strict_types=1);

namespace PeServer\Core;

class ArrayUtility
{
	public static function isNullOrEmpty(?array $array): bool
	{
		if (is_null($array)) {
			return true;
		}
		return count($array) === 0;
	}
}
