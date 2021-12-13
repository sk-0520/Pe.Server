<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Throwable;
use \PeServer\Core;

class ParseException extends CoreException
{
	public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
