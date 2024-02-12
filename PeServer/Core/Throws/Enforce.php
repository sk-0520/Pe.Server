<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Code;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\EnforceClassNameError;
use PeServer\Core\Throws\EnforceException;
use PeServer\Core\Type;
use TypeError;

/**
 * 強制処理。
 */
abstract class Enforce
{
	#region function

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
			throw new EnforceClassNameError($exceptionClass);
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
	public static function throwIf(bool $value, string $argument = '', string $exceptionClass = EnforceException::class): void
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
	public static function throwIfNull(mixed $value, string $argument = '', string $exceptionClass = EnforceException::class): void
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
	 */
	public static function throwIfNullOrEmpty(?string $value, string $argument = '', string $exceptionClass = EnforceException::class): void
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
	 */
	public static function throwIfNullOrWhiteSpace(?string $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (Text::isNullOrWhiteSpace($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	#endregion
}
