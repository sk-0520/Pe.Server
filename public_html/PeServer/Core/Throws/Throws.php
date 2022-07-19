<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Type;

abstract class Throws
{
	public static function getErrorCode(Throwable $throwable): int
	{
		$rawCode = $throwable->getCode();
		$code = 0;
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
		$rawCode = $previous->getCode();
		$code = 0;
		if (is_integer($rawCode)) {
			$code = $rawCode;
		}

		/** @var Throwable */
		$exception = ReflectionUtility::create($className, Throwable::class, $message, $code, $previous);
		throw $exception;
	}
}
