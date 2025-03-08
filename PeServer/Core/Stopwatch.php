<?php

declare(strict_types=1);

namespace PeServer\Core;

use DateInterval;
use Stringable;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\StopwatchWException;

/**
 * `HRTime\StopWatch` 的な。
 *
 * あと簡単な時間系処理のラッパー。
 */
class Stopwatch implements Stringable
{
	#region variable

	/** 計測中か。 */
	private bool $isRunning = false;
	/** 計測開始時間。 */
	private int $startTime = 0;
	/** 計測終了時間。 */
	private int $stopTime = 0;

	#endregion

	#region function

	/**
	 * インスタンス生成しつつ計測開始。
	 *
	 * @return self
	 */
	public static function startNew(): self
	{
		$result = new self();
		$result->start();
		return $result;
	}

	/**
	 * 計測中か。
	 *
	 * @return bool
	 */
	public function isRunning(): bool
	{
		return $this->isRunning;
	}

	/**
	 * 計測開始。
	 *
	 * @throws InvalidOperationException 現在計測中。
	 */
	public function start(): void
	{
		if ($this->isRunning) {
			throw new InvalidOperationException();
		}

		$this->isRunning = true;
		$time = self::getCurrentTime();
		$this->startTime = $time;
		$this->stopTime = $time;
	}

	/**
	 * 計測終了。
	 */
	public function stop(): void
	{
		$this->stopTime = self::getCurrentTime();
		$this->isRunning = false;
	}

	public function restart(): void
	{
		$this->stop();
		$this->start();
	}

	/**
	 * 現在の経過時間(ナノ秒)を取得。
	 *
	 * `self::getElapsed` を原則使用する想定。
	 *
	 * * 計測中であれば計測開始からの経過時間
	 * * 計測終了であれば計測開始からの計測終了までの経過時間。
	 *
	 * @return int
	 */
	public function getNanosecondsElapsed(): int
	{
		if ($this->isRunning) {
			return self::getCurrentTime() - $this->startTime;
		}

		return $this->stopTime - $this->startTime;
	}

	/**
	 * 現在の経過時間を取得。
	 *
	 * * 計測中であれば計測開始からの経過時間
	 * * 計測終了であれば計測開始からの計測終了までの経過時間。
	 *
	 * @return DateInterval
	 */
	public function getElapsed(): DateInterval
	{
		$nano = $this->getNanosecondsElapsed();
		$usec = self::nanoToMicrosecounds($nano);

		// TODO: 結構適当なんでいつか直す
		$s = (int)$usec;
		$f = 0 < $s ? $s - $usec : $usec;
		$f = (string)$f;
		if (Text::contains($f, ".", false)) {
			$f = Text::trimStart(Text::split((string)$f, ".")[1], "0");
		}

		$result = DateInterval::createFromDateString("{$s} second {$f} microseconds");
		if ($result === false) {
			throw new StopwatchWException();
		}
		return $result;
	}

	/**
	 * ミリ秒として文字列化。
	 *
	 * @return string
	 */
	public function toString(): string
	{
		return self::nanoToMilliseconds($this->getNanosecondsElapsed()) . ' msec';
	}

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

	//@phpstan-ignore-next-line 32bit(笑)
	private static function getCurrentTime32(): float|false
	{
		throw new NotSupportedException();
	}
	/**
	 * 64bit環境用 `hrtime`
	 *
	 * @return int
	 */
	private static function getCurrentTime64(): int
	{
		$result = hrtime(true);
		if ($result === false) { //@phpstan-ignore-line 失敗したら false 返ってくるっぽいんだけどなぁ
			throw new StopwatchWException();
		}

		return $result;
	}
	/**
	 * 現在のナノ秒を取得。
	 *
	 * @return int
	 */
	public static function getCurrentTime(): int
	{
		return self::getCurrentTime64();
	}

	public static function nanoToMicrosecounds(int $nanoSec): float
	{
		return $nanoSec / 1e+9;
	}

	public static function nanoToMilliseconds(int $nanoSec): float
	{
		return $nanoSec / 1e+6;
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString();
	}

	#endregion
}
