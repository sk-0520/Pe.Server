<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\ArgumentException;

class ArgumentNullException extends ArgumentException
{
	use ThrowableTrait;

	#region function

	public static function throwIfNull(mixed $argument, string $name = ''): void
	{
		if ($argument === null) {
			throw new ArgumentNullException($name);
		}
	}

	#endregion
}
