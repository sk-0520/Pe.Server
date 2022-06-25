<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\StringUtility;
use PeServer\Core\Throws\EnforceException;

/**
 * 強制処理。
 */
abstract class Enforce
{
	private static function throwCore(string $argument, string $exceptionClass)
	{
		$exception = new $exceptionClass($argument);
		throw $exception; // あかんかったら死のう
	}

	public static function throwIf(bool $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (!$value) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	public static function throwIfNull(mixed $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (is_null($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	public static function throwIfNullOrEmpty(?string $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (StringUtility::isNullOrEmpty($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}

	public static function throwIfNullOrWhiteSpace(?string $value, string $argument = '', string $exceptionClass = EnforceException::class): void
	{
		if (StringUtility::isNullOrWhiteSpace($value)) {
			self::throwCore($argument, $exceptionClass);
		}
	}
}
