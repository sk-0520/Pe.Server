<?php

declare(strict_types=1);

namespace PeServer\Core;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PeServer\Core\Throws\DateTimeException;
use PeServer\Core\Throws\ParseException;

/**
 * UTCの生成・読み込み処理
 *
 * UTC扱うのにすっげー遠回りしたぞ。
 */
abstract class Utc
{
	#region define

	private const UTC_FORMAT_01 = 'Y-m-d\TH:i:s.u\Z';
	private const UTC_FORMAT_02 = 'Y-m-d\TH:i:s\Z';
	private const UTC_FORMAT_03 = 'Y-m-d\TH:i:s.u';
	private const UTC_FORMAT_04 = 'Y-m-d\TH:i:s';

	private const UTC_FORMAT_11 = 'Y-m-d H:i:s.u\Z';
	private const UTC_FORMAT_12 = 'Y-m-d H:i:s\Z';
	private const UTC_FORMAT_13 = 'Y-m-d H:i:s.u';
	private const UTC_FORMAT_14 = 'Y-m-d H:i:s';

	private const UTC_FORMAT_21 = 'Y/m/d H:i:s.u\Z';
	private const UTC_FORMAT_22 = 'Y/m/d H:i:s\Z';
	private const UTC_FORMAT_23 = 'Y/m/d H:i:s.u';
	private const UTC_FORMAT_24 = 'Y/m/d H:i:s';

	#endregion

	#region variable

	private static ?DateTimeZone $timezone = null;

	#endregion

	#region function

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
	 * 現在時刻を変更可能なオブジェクトとして取得。
	 *
	 * @return DateTime
	 */
	public static function createDateTime(): DateTime
	{
		return new DateTime('now', self::getTimezone());
	}

	/**
	 * パース処理。
	 *
	 * @param class-string $dateTimeClassName
	 * @phpstan-param class-string<DateTimeImmutable|DateTime> $dateTimeClassName
	 * @param string|null $s
	 * @param DateTimeImmutable|DateTime|null $result
	 * @return boolean
	 * @phpstan-assert-if-true DateTimeImmutable|DateTime $result
	 * @phpstan-assert-if-false null $result
	 */
	private static function tryParseCore(string $dateTimeClassName, ?string $s, DateTimeImmutable|DateTime|null &$result): bool
	{
		if ($s === null) {
			return false;
		}

		$formats = [
			self::UTC_FORMAT_01,
			self::UTC_FORMAT_02,
			self::UTC_FORMAT_03,
			self::UTC_FORMAT_04,
			self::UTC_FORMAT_11,
			self::UTC_FORMAT_12,
			self::UTC_FORMAT_13,
			self::UTC_FORMAT_14,
			self::UTC_FORMAT_21,
			self::UTC_FORMAT_22,
			self::UTC_FORMAT_23,
			self::UTC_FORMAT_24,
		];

		$datetime = false;
		foreach ($formats as $format) {
			$datetime = $dateTimeClassName::createFromFormat($format, $s, self::getTimezone());
			if ($datetime !== false) {
				break;
			}
		}
		if ($datetime === false) {
			$result = null;
			return false;
		}

		$result = $datetime;
		return true;
	}

	/**
	 * パース処理。
	 *
	 * @param string|null $s
	 * @param DateTimeImmutable|null $result
	 * @return boolean パース成功状態。
	 * @phpstan-assert-if-true DateTimeImmutable $result
	 * @phpstan-assert-if-false null $result
	 */
	public static function tryParse(?string $s, ?DateTimeImmutable &$result): bool
	{
		//@phpstan-ignore-next-line [TIME]
		return self::tryParseCore(DateTimeImmutable::class, $s, $result);
	}

	/**
	 * 変更可能なオブジェクトとしてパース処理。
	 *
	 * @param string|null $s
	 * @param-out DateTime $result
	 * @return boolean パース成功状態。
	 */
	public static function tryParseDateTime(?string $s, ?DateTime &$result): bool
	{
		//@phpstan-ignore-next-line [TIME]
		return self::tryParseCore(DateTime::class, $s, $result);
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
	 * 変更可能なオブジェクトとしてパース処理。
	 *
	 * @param string $s
	 * @return DateTime
	 * @throws ParseException
	 */
	public static function parseDateTime(string $s): DateTime
	{
		if (self::tryParseDateTime($s, $result)) {
			return $result;
		}

		throw new ParseException();
	}

	/**
	 * 文字列化。
	 *
	 * @param DateTime|DateTimeImmutable|DateTimeInterface $datetime
	 * @return string
	 */
	public static function toString(DateTime|DateTimeImmutable|DateTimeInterface $datetime): string
	{
		return $datetime->format(self::UTC_FORMAT_01);
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

	#endregion
}
