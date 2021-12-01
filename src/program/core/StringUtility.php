<?php declare(strict_types=1);

class StringUtility
{
	public static function isNullOrEmpty(?string $s): bool
	{
		if(is_null($s)) {
			return true;
		}

		if($s === '0') {
			return false;
		}

		return empty($s);
	}
}
