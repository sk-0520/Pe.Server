<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\ArgumentException;

class ArgumentNullException extends ArgumentException
{
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	#region function

	public static function throwIfNull(mixed $argument, string $name = ''): void
	{
		if (is_null($argument)) {
			throw new ArgumentNullException($name);
		}
	}

	#endregion
}
