<?php

declare(strict_types=1);

namespace PeServer\Core;

use \DateTimeImmutable;
use \DateTimeZone;
use PeServer\Core\Throws\ParseException;

/**
 * UTCの生成・読み込み処理
 *
 * UTC扱うのにすっげー遠回りしたぞ。
 */
abstract class Utc
{
	private const UTC_FORMAT = 'Y-m-d\TH:i:s.u\Z';
	private static ?DateTimeZone $timezone = null;

	public static function getTimezone(): DateTimeZone
	{
		return self::$timezone ??= new DateTimeZone('UTC');
	}

	public static function create(): DateTimeImmutable
	{
		return new DateTimeImmutable('now', self::getTimezone());
	}

	public static function tryParse(string $s, ?DateTimeImmutable &$result): bool
	{
		$datetime = DateTimeImmutable::createFromFormat(self::UTC_FORMAT, $s, self::getTimezone());
		if ($datetime === false) {
			return false;
		}

		$result = $datetime;
		return true;
	}

	public static function parse(string $s): DateTimeImmutable
	{
		if (self::tryParse($s, $result)) {
			return $result;
		}

		throw new ParseException();
	}

	public static function toString(DateTimeImmutable $datetime): string
	{
		return $datetime->format(self::UTC_FORMAT);
	}

	public static function createString(): string
	{
		return self::toString(self::create());
	}
}
