<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\EnforceException;
use PeServer\Core\Throws\EnforceClassNameError;

/**
 * 強制処理。
 */
abstract class Enforce
{
	/**
	 * 例外ぶん投げ。
	 *
	 * @param string $argument
	 * @param string $exceptionClass
	 * @phpstan-param class-string $exceptionClass
	 * @return no-return
	 */
	private static function throwCore(string $argument, string $exceptionClass)
	{
		$exception = new $exceptionClass($argument);
		if ($exception instanceof Throwable) {
			throw $exception;
		}

		throw new EnforceClassNameError($exceptionClass);
	}

	/**
	 * 偽の場合に例外。
	 *
	 * @param boolean $value
	 * @param string $argument
	 * @param string $exceptionClass
	 * @phpstan-param class-string $exceptionClass
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
	 * @param string $exceptionClass
	 * @phpstan-param class-string $exceptionClass
	 * @return void
	 */
	public static function throwIfNull(mixed $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (is_null($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	/**
	 * 文字列がnullか空の場合に例外。
	 *
	 * @param string|null $value
	 * @param string $argument
	 * @param string $exceptionClass
	 * @phpstan-param class-string $exceptionClass
	 * @return void
	 */
	public static function throwIfNullOrEmpty(?string $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (StringUtility::isNullOrEmpty($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	/**
	 * 文字列がnullかホワイトスペースのみの場合に例外。
	 *
	 * @param string|null $value
	 * @param string $argument
	 * @param string $exceptionClass
	 * @phpstan-param class-string $exceptionClass
	 * @return void
	 */
	public static function throwIfNullOrWhiteSpace(?string $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (StringUtility::isNullOrWhiteSpace($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}
}
