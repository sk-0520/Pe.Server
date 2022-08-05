<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\ArrayUtility;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Type;
use TypeError;

/**
 * 例外処理系。
 */
abstract class Throws
{
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
		if (is_integer($rawCode)) {
			$code = $rawCode;
		}

		return $code;
	}

	/**
	 * 例外の再スロー。
	 *
	 * @param string $className 例外名。
	 * @phpstan-param class-string<Throwable> $className 例外名。
	 * @param Throwable $previous ラップする元の例外。
	 * @return no-return
	 */
	public static function reThrow(string $className, Throwable $previous, string $message = null): void
	{
		$message = $message ?? $previous->getMessage();
		$code = self::getErrorCode($previous);

		/** @var Throwable */
		$exception = ReflectionUtility::create($className, Throwable::class, $message, $code, $previous);
		throw $exception;
	}

	/**
	 * 対象の例外を受け取った場合に指定した例外として再送出する。
	 *
	 * @template TResult
	 * @param string|array<string> $catchExceptions 対象の例外名（複数ある場合は配列で指定）。
	 * @phpstan-param class-string<Throwable>|non-empty-array<class-string<Throwable>> $catchExceptions
	 * @param string $throwException 再送出する例外名。
	 * @phpstan-param class-string<Throwable> $throwException
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
		} else if (ArrayUtility::isNullOrEmpty($catchExceptions)) { //@phpstan-ignore-line non-empty-array
			throw new TypeError('array: $catchException');
		}

		foreach ($catchExceptions as $key => $catchException) {
			if (!is_subclass_of($catchException, Throwable::class)) { //@phpstan-ignore-line class-string<Throwable>
				throw new TypeError('$catchException[' . $key . ']: ' . $catchException);
			}
		}

		if (!is_subclass_of($throwException, Throwable::class)) { //@phpstan-ignore-line class-string<Throwable>
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
}
