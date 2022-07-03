<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * コーディング上のあれ。
 */
abstract class Code
{
	/**
	 * 疑似コード: C# using
	 *
	 * @template TDisposable of IDisposable
	 * @template TResult
	 * @param IDisposable $disposable
	 * @phpstan-param TDisposable $disposable
	 * @param callable $callback
	 * @phpstan-param (callable(TDisposable $disposable): TResult) $callback
	 * @return mixed
	 * @phpstan-return TResult
	 */
	public static function using(IDisposable $disposable, callable $callback)
	{
		try {
			return $callback($disposable);
		} finally {
			$disposable->dispose();
		}
	}
}
