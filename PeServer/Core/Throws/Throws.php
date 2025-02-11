<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use TypeError;
use PeServer\Core\Collection\Arr;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Text;
use PeServer\Core\Type;

/**
 * 例外処理系。
 */
abstract class Throws
{
	#region function

	/**
	 * `Throwable::getCode` のラッパー。
	 *
	 * なんかもうつらい。
	 *
	 * @param Throwable $throwable
	 * @return int 取得したエラーコード。取得できなかった場合は `PHP_INT_MIN` を返す。
	 */
	public static function getErrorCode(Throwable $throwable): int
	{
		$rawCode = $throwable->getCode();
		$code = PHP_INT_MIN;
		if (is_int($rawCode)) {
			$code = $rawCode;
		}

		return $code;
	}

	/**
	 * 例外の再スロー。
	 *
	 * @template TException of Throwable
	 * @param string $className 例外名。
	 * @phpstan-param class-string<TException> $className
	 * @param Throwable $previous ラップする元の例外。
	 * @throws TException
	 */
	public static function reThrow(string $className, Throwable $previous, string $message = null): never
	{
		$message = $message ?? $previous->getMessage();
		$code = self::getErrorCode($previous);

		$exception = ReflectionUtility::create($className, Throwable::class, $message, $code, $previous);
		throw $exception;
	}

	/**
	 * 対象の例外を受け取った場合に指定した例外として再送出する。
	 *
	 * @template TResult
	 * @param $catchExceptions 対象の例外名（複数ある場合は配列で指定）。
	 * @phpstan-param class-string<Throwable>|non-empty-array<class-string<Throwable>> $catchExceptions 対象の例外名（複数ある場合は配列で指定）。
	 * @param class-string<Throwable> $throwException 再送出する例外名。
	 * @param callable $callback 例外を発生させる可能性のある処理。
	 * @phpstan-param callable():TResult $callback
	 * @return mixed `$callback` が戻り値を持つ場合にその値。戻り値を持たない場合は `null`。
	 * @phpstan-return TResult
	 * @throws TypeError 入力パラメータが不正。
	 */
	public static function wrap(string|array $catchExceptions, string $throwException, callable $callback): mixed
	{
		if (is_string($catchExceptions)) {
			$catchExceptions = [$catchExceptions];
		} elseif (Arr::isNullOrEmpty($catchExceptions)) { //@phpstan-ignore-line [DOCTYPE] non-empty-array
			throw new TypeError('array: $catchException');
		}

		foreach ($catchExceptions as $key => $catchException) {
			if (!is_subclass_of($catchException, Throwable::class)) { //@phpstan-ignore-line [DOCTYPE] class-string<Throwable>
				throw new TypeError('$catchException[' . $key . ']: ' . $catchException);
			}
		}

		if (!is_subclass_of($throwException, Throwable::class)) { //@phpstan-ignore-line [DOCTYPE] class-string<Throwable>
			throw new TypeError('$throwException');
		}

		try {
			return $callback();
		} catch (Throwable $throwable) {
			foreach ($catchExceptions as $key => $catchException) {
				if (is_a($throwable, $catchException)) {
					self::reThrow($throwException, $throwable);
				}
			}
			throw $throwable;
		}
	}

	/**
	 * 例外ぶん投げ。
	 *
	 * @param string $argument
	 * @param class-string<Throwable> $exceptionClass
	 */
	private static function throwCore(string $argument, string $exceptionClass): never
	{
		try {
			$exception = ReflectionUtility::create($exceptionClass, Throwable::class, $argument);
		} catch (TypeError $ex) {
			throw new InvalidClassNameError($exceptionClass);
		}

		throw $exception;
	}

	/**
	 * 偽の場合に例外。
	 *
	 * @param boolean $value
	 * @param string $argument
	 * @param class-string<Throwable> $exceptionClass
	 * @return void
	 */
	public static function throwIf(bool $value, string $argument = '', string $exceptionClass = InvalidException::class): void
	{
		if (!$value) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	/**
	 * nullの場合に例外。
	 *
	 * @param mixed $value
	 * @param string $argument
	 * @param class-string<Throwable> $exceptionClass
	 * @return void
	 */
	public static function throwIfNull(mixed $value, string $argument = '', string $exceptionClass = InvalidException::class): void
	{
		if ($value === null) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	/**
	 * 文字列がnullか空の場合に例外。
	 *
	 * @param string|null $value
	 * @param string $argument
	 * @param class-string<Throwable> $exceptionClass
	 * @return void
	 * @phpstan-assert non-empty-string $value
	 */
	public static function throwIfNullOrEmpty(?string $value, string $argument = '', string $exceptionClass = InvalidException::class): void
	{
		if (Text::isNullOrEmpty($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	/**
	 * 文字列がnullかホワイトスペースのみの場合に例外。
	 *
	 * @param string|null $value
	 * @param string $argument
	 * @param class-string<Throwable> $exceptionClass
	 * @return void
	 * @phpstan-assert non-empty-string $value
	 */
	public static function throwIfNullOrWhiteSpace(?string $value, string $argument = '', string $exceptionClass = InvalidException::class): void
	{
		if (Text::isNullOrWhiteSpace($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	#endregion
}
