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

	public static function getOr(array $array, $key, $defaultValue)
	{
		if (isset($array[$key])) {
			return $array[$key];
		}

		return $defaultValue;
	}

	public static function tryGet(array $array, $key, &$result): bool
	{
		if (isset($array[$key])) {
			$result = $array[$key];
			return true;
		}

		return false;
	}
}
