<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\NotImplementedException;
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
	 * @param string|object $input 生成クラス名。オブジェクトを渡した場合は型チェックを行うのみになる。
	 * @phpstan-param class-string|TObject $input
	 * @param string|object $baseClass 基底クラス。オブジェクトを渡した場合は生成クラスの型チェックに使用される。
	 * @phpstan-param class-string|object $baseClass
	 * @return object 生成クラス。 $input がオブジェクトの場合は生成しない。
	 * @phpstan-return TObject
	 * @throws TypeException 型おかしい。
	 */
	public static function create(string|object $input, string|object $baseClass, mixed ...$parameters): object
	{
		if (is_string($input)) {
			$input = new $input(...$parameters);
		}

		if (is_string($baseClass)) {
			if (!is_a($input, $baseClass, false)) {
				throw new TypeException();
			}
		} else {
			$baseClassName = get_class($baseClass);
			if (!is_a($input, $baseClassName, false)) {
				throw new TypeException();
			}
		}

		/** @phpstan-var TObject */
		return $input;
	}
}
