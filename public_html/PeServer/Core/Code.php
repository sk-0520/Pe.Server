<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\TypeException;

/**
 * コーディング上のあれ。
 */
abstract class Code
{
	#region function

	/**
	 * 文字列をリテラル文字列に変換。
	 *
	 * PHPStan用のラッパー(関数にしとけば後で探すの楽でしょ感で作った)。
	 *
	 * @param string $s
	 * @phpstan-return literal-string
	 */
	public static function toLiteralString(string $s): string
	{
		/** @phpstan-var literal-string */
		return $s;
	}

	/**
	 * 疑似コード: C# using
	 *
	 * @template TDisposable of IDisposable
	 * @template TResult
	 * @param IDisposable $disposable
	 * @phpstan-param TDisposable $disposable
	 * @param callable $callback
	 * @phpstan-param callable(TDisposable $disposable):TResult $callback
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

	public static function toString(object $obj, string $text): string
	{
		return get_class($obj) . ': ' . $text;
	}

	#endregion
}
