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
	 * @param string $input 生成クラス名。
	 * @phpstan-param class-string<TObject> $input
	 * @param string|object $baseClass 基底クラス。オブジェクトを渡した場合は生成クラスの型チェックに使用される。
	 * @phpstan-param class-string|object $baseClass
	 * @return object 生成インスタンス。
	 * @phpstan-return TObject
	 * @throws TypeException 型おかしい。
	 */
	public static function create(string $input, string|object $baseClass, mixed ...$parameters): object
	{
		if (!class_exists($input)) {
			throw new TypeException();
		}

		$input = new $input(...$parameters);

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

	/**
	 * `method_exists` ラッパー。
	 *
	 * @param string|object $input
	 * @phpstan-param class-string|object $input
	 * @param string $method
	 * @phpstan-param non-empty-string $method
	 */
	public static function existsMethod(object|string $input, string $method): bool
	{
		if (is_string($input)) {
			if (StringUtility::isNullOrWhiteSpace($input)) { //@phpstan-ignore-line
				throw new ArgumentException('$input');
			}
		}

		if (StringUtility::isNullOrWhiteSpace($method)) { //@phpstan-ignore-line
			throw new ArgumentException('$method');
		}

		return method_exists($input, $method);
	}
}
