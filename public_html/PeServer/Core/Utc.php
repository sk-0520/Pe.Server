<?php

declare(strict_types=1);

namespace PeServer\Core;

use \DateTime;
use \DateTimeZone;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ParseException;

/**
 * UTCの生成・読み込み処理
 *
 * UTC扱うのにすっげー遠回りしたぞ。
 */
abstract class Utc
{
	private static ?DateTimeZone $timezone = null;

	public static function getTimezone(): DateTimeZone
	{
		return self::$timezone ??= new DateTimeZone('UTC');
	}

	public static function create(): DateTime
	{
		return new DateTime('now', self::getTimezone());
	}

	public static function tryParse(string $s, ?DateTime &$result): bool
	{
		$datetime = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $s, self::getTimezone());
		if ($datetime === false) {
			return false;
		}

		$result = $datetime;
		return true;
	}

	public static function parse(string $s): DateTime
	{
		if (self::tryParse($s, $result)) {
			return $result;
		}

		throw new ParseException();
	}

	public static function toString(DateTime $datetime): string
	{
		return $datetime->format('Y-m-d\TH:i:s\Z');
	}

	public static function createString(): string
	{
		return self::toString(self::create());
	}
}
