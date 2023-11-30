<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collections\Arr;
use PeServer\Core\IDisposable;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\TypeException;
use ReflectionClass;

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
	 * これつっかえんわぁ。。。
	 * `=>` が複数行使えればなぁ。
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

	/**
	 * @deprecated toString2 に置き換え後、 toString に差し替える。
	 */
	public static function toString(object $obj, string $text): string
	{
		return get_class($obj) . ': ' . $text;
	}

	/**
	 *
	 * @param object $obj
	 * @param string[] $propertyNames
	 * @param string $separator
	 * @return string
	 */
	public static function toString2(object $obj, array $propertyNames, string $separator = ','): string
	{
		$rc = new ReflectionClass($obj);

		return
			get_class($obj) .
			'(' .
			Text::join($separator, Arr::map($propertyNames, fn ($a) => $a . ':' . $rc->getProperty($a)->getValue($obj))) .
			')';
	}

	#endregion
}
