<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * メモリ使用状況のあれこれ。
 *
 * 原則使用しない。
 *
 * @codeCoverageIgnore
 */
abstract class Memory
{
	#region function

	/**
	 * 現在の使用メモリ量を取得。
	 *
	 * `memory_get_usage(false)` ラッパー。
	 *
	 * @return int
	 * @see https://www.php.net/manual/function.memory-get-usage.php
	 */
	public static function getUsage(): int
	{
		return memory_get_usage(false);
	}

	/**
	 * システム割り当てメモリ量を取得。
	 *
	 * `memory_get_usage(false)` ラッパー。
	 *
	 * @return int
	 * @see https://www.php.net/manual/function.memory-get-usage.php
	 */
	public static function getAllocate(): int
	{
		return memory_get_usage(true);
	}

	/**
	 * スクリプトのメモリ最大使用量を取得。
	 *
	 * `memory_get_peak_usage(false)` ラッパー。
	 *
	 * @return int
	 * @see https://www.php.net/manual/function.memory-get-peak-usage.php
	 */
	public static function getPeakUsage(): int
	{
		return memory_get_peak_usage(false);
	}

	/**
	 * スクリプトのシステム割り当てメモリ最大使用量を取得。
	 *
	 * `memory_get_peak_usage(true)` ラッパー。
	 *
	 * @return int
	 * @see https://www.php.net/manual/function.memory-get-peak-usage.php
	 */
	public static function getPeakAllocate(): int
	{
		return memory_get_peak_usage(true);
	}

	#endregion
}
