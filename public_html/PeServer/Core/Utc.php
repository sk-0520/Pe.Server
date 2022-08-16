<?php

declare(strict_types=1);

namespace PeServer\Core;

use \DateTimeImmutable;
use \DateTimeInterface;
use \DateTimeZone;
use DateTime;
use PeServer\Core\Throws\DateTimeException;
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

	/**
	 * タイムゾーン取得。
	 *
	 * @return DateTimeZone
	 */
	public static function getTimezone(): DateTimeZone
	{
		return self::$timezone ??= new DateTimeZone('UTC');
	}

	/**
	 * 現在時刻を取得。
	 *
	 * @return DateTimeImmutable
	 */
	public static function create(): DateTimeImmutable
	{
		return new DateTimeImmutable('now', self::getTimezone());
	}

	/**
	 * パース処理。
	 *
	 * @param string $s
	 * @param DateTimeImmutable|null $result
	 * @return boolean
	 */
	public static function tryParse(string $s, ?DateTimeImmutable &$result): bool
	{
		$datetime = DateTimeImmutable::createFromFormat(self::UTC_FORMAT, $s, self::getTimezone());
		if ($datetime === false) {
			return false;
		}

		$result = $datetime;
		return true;
	}

	/**
	 * パース処理。
	 *
	 * @param string $s
	 * @return DateTimeImmutable
	 * @throws ParseException
	 */
	public static function parse(string $s): DateTimeImmutable
	{
		if (self::tryParse($s, $result)) {
			return $result;
		}

		throw new ParseException();
	}

	/**
	 * 文字列化。
	 *
	 * @param DateTimeImmutable $datetime
	 * @return string
	 */
	public static function toString(DateTimeInterface $datetime): string
	{
		return $datetime->format(self::UTC_FORMAT);
	}

	/**
	 * 現在日時を文字列化。
	 *
	 * @return string
	 */
	public static function createString(): string
	{
		return self::toString(self::create());
	}

	/**
	 * UNIX時間からDateTimeに変換。
	 *
	 * @param int $unixTime
	 * @return DateTime
	 * @throws DateTimeException
	 */
	public static function toEditableDateTimeFromUnixTime(int $unixTime): DateTime
	{
		$result = DateTime::createFromFormat('U', (string)$unixTime, self::getTimezone());
		if ($result === false) {
			throw new DateTimeException();
		}

		return $result;
	}

	/**
	 * UNIX時間からDateTimeImmutableに変換。・
	 *
	 * @param int $unixTime
	 * @return DateTimeImmutable
	 * @throws DateTimeException
	 */
	public static function toDateTimeFromUnixTime(int $unixTime): DateTimeImmutable
	{
		$result = DateTimeImmutable::createFromFormat('U', (string)$unixTime, self::getTimezone());
		if ($result === false) {
			throw new DateTimeException();
		}

		return $result;
	}
}
