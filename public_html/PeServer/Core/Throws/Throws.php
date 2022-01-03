<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Exception;
use \Throwable;

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

		/** @var Exception */
		$exception = new $className($message, $code, $previous);
		throw $exception;
	}
}
