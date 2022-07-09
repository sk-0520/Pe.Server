<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\TypeException;

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

	/**
	 * クラスオブジェクトの生成。
	 *
	 * @template TObject of object
	 * @param string|object $input
	 * @phpstan-param class-string|TObject $input
	 * @param string $baseClass
	 * @phpstan-param class-string $baseClass
	 * @return object
	 * @phpstan-return TObject
	 */
	public static function create(string|object $input, string $baseClass): object
	{
		if (is_string($input)) {
			$input = new $input();
		}

		if (!is_a($input, $baseClass, false)) {
			throw new TypeException();
		}

		/** @phpstan-var TObject */
		return $input;
	}
}
