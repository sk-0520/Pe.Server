<?php

declare(strict_types=1);

namespace PeServer\Core;

class StringUtility
{
	public static function isNullOrEmpty(?string $s): bool
	{
		if (is_null($s)) {
			return true;
		}

		if ($s === '0') {
			return false;
		}

		return empty($s);
	}

	public static function isNullOrWhiteSpace(?string $s): bool
	{
		if (self::isNullOrEmpty($s)) {
			return true;
		}

		return strlen(trim($s)) === 0;
	}
}
