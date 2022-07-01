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
	 * @param IDisposable $disposable
	 * @phpstan-param TDisposable $disposable
	 * @param callable $callback
	 * @phpstan-param (callable(TDisposable $context): void) $callback
	 * @return void
	 */
	public static function using(IDisposable $disposable, callable $callback)
	{
		try {
			$callback($disposable);
		} finally {
			$disposable->dispose();
		}
	}
}
