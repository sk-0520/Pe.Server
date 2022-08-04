<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * `HRTime\StopWatch` 的な。
 */
class Timer
{
	/**
	 * 現在のUNIX時間(秒)を取得。
	 *
	 * `time` ラッパー。
	 *
	 * @return int
	 * @see https://www.php.net/manual/function.time.php
	 */
	public static function getUnixTime(): int
	{
		return time();
	}

	/**
	 * 現在のUNIX時間(マイクロ秒)を取得。
	 *
	 * `microtime` ラッパー。
	 *
	 * @return float
	 * @see https://www.php.net/manual/function.microtime.php
	 */
	public static function getUnixMicroTime(): float
	{
		return microtime(true);
	}
}
