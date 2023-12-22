<?php

declare(strict_types=1);

namespace PeServer\Core;

use DateInterval;
use Exception;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FormatException;
use PeServer\Core\Throws\Throws;

/**
 * `DateInterval` 処理。
 */
abstract class Time
{
	#region define

	/** ISO 8601 書式 */
	public const FORMAT_ISO8601 = 0;
	/** D.HH:MM:SS */
	public const FORMAT_READABLE = 1;

	#endregion

	#region function

	/**
	 * 全体秒を取得。
	 *
	 * @param DateInterval $time
	 * @return int
	 */
	public static function getTotalSeconds(DateInterval $time): int
	{
		$totalSeconds
			= ($time->s)
			+ ($time->i * 60)
			+ ($time->h * 60 * 60)
			+ ($time->d * 60 * 60 * 24)
			+ ($time->m * 60 * 60 * 24 * 30)
			+ ($time->y * 60 * 60 * 24 * 365);

		return $totalSeconds;
	}

	/**
	 * 全体秒から時間を生成。
	 *
	 * @param int $totalSeconds 全体秒。
	 * @return DateInterval
	 */
	public static function createFromSeconds(int $totalSeconds): DateInterval
	{
		$interval = new DateInterval('PT' . abs($totalSeconds) . 'S');
		$current = Utc::create();

		$diffValue = 0 < $totalSeconds
			? $current->sub($interval)
			: $current->add($interval);

		$timeSpan = $diffValue->diff($current);

		return $timeSpan;
	}

	private static function createISO8601(string $time): DateInterval
	{
		return Throws::wrap(Exception::class, FormatException::class, fn () => new DateInterval($time));
	}

	private static function createReadable(string $time, ?Encoding $encoding = null): DateInterval
	{
		//TODO: 秒未満未対応かぁ～

		$regex = new Regex($encoding);
		$matches = $regex->matches($time, '/\A((?<DAY>\d+)\.)?(?<H>\d+):(?<M>\d+):(?<S>\d+)\z/');

		if (Arr::isNullOrEmpty($matches)) {
			throw new FormatException($time);
		}

		$totalSeconds
			= ((int)$matches['S'])
			+ ((int)$matches['M'] * 60)
			+ ((int)$matches['H'] * 60 * 60)
			+ (isset($matches['DAY']) ? (int)$matches['DAY'] * 60 * 60 * 24 : 0);

		$timeSpan = self::createFromSeconds($totalSeconds);

		return $timeSpan;
	}

	private static function createConstructor(string $time): DateInterval
	{
		$result = ErrorHandler::trap(fn () => DateInterval::createFromDateString($time));
		if ($result->isFailureOrFalse()) {
			throw new FormatException($time);
		}

		return $result->value;
	}

	/**
	 * 文字列から時間を生成。
	 *
	 * @param string $time 時間を表す文字列
	 *               1. `ISO8601`
	 *               2. `DAY.HH:MM:SS`
	 *               3. `DateInterval::__constructor`
	 * @param Encoding|null $encoding HH:MM:SS 形式の場合のエンコーディング。 **指定する必要なし**。
	 * @return DateInterval
	 * @throws ArgumentException 引数が空。
	 * @throws FormatException 書式が腐ってる。
	 */
	public static function create(string $time, ?Encoding $encoding = null): DateInterval
	{
		if (Text::isNullOrWhiteSpace($time)) {
			throw new ArgumentException('$time');
		}

		if ($time[0] === 'P') {
			return self::createISO8601($time);
		}

		if (Text::contains($time, ':', false)) {
			return self::createReadable($time, $encoding);
		}

		return self::createConstructor($time);
	}

	private static function toStringISO8601(DateInterval $time): string
	{
		$buffer = 'P';

		$hasDate = $time->y || $time->m || $time->d;
		if ($hasDate) {
			$buffer .= $time->y ? $time->y . 'Y' : '';
			$buffer .= $time->m ? $time->m . 'M' : '';
			$buffer .= $time->d ? $time->d . 'D' : '';
		}

		$hasTime = $time->h || $time->i || $time->s;
		if ($hasTime) {
			$buffer .= 'T';
			$buffer .= $time->h ? $time->h . 'H' : '';
			$buffer .= $time->i ? $time->i . 'M' : '';
			$buffer .= $time->s ? $time->s . 'S' : '';
		}

		return $buffer;
	}

	private static function toStringReadable(DateInterval $time): string
	{
		$totalSeconds = self::getTotalSeconds($time);

		$timeSpan = self::createFromSeconds($totalSeconds);

		$buffer = '';
		if ($timeSpan->days) {
			$buffer .= $timeSpan->days . '.';
		}

		$buffer .= $timeSpan->format('%H:%I:%S');

		return $buffer;
	}

	/**
	 * `DateInterval` の文字列化。
	 *
	 * @param DateInterval $time
	 * @param int $format
	 * @phpstan-param self::FORMAT_* $format
	 * @return string
	 */
	public static function toString(DateInterval $time, int $format): string
	{
		return match ($format) {
			self::FORMAT_ISO8601 => self::toStringISO8601($time),
			self::FORMAT_READABLE => self::toStringReadable($time),
		};
	}

	#endregion
}
