<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\ArgumentException;

class ArgumentNullException extends ArgumentException
{
	use ThrowableTrait;

	#region function

	/**
	 * `null` の場合に `self` を投げる。
	 * @param mixed $argument 評価対象。
	 * @param string $name
	 * @throws ArgumentNullException
	 * @phpstan-assert !null $argument
	 */
	public static function throwIfNull(mixed $argument, string $name = ''): void
	{
		if ($argument === null) {
			throw new ArgumentNullException($name);
		}
	}

	#endregion
}
