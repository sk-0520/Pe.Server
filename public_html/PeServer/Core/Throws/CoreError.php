<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use \Error;
use PeServer\Core\Throws;

class CoreError extends Error
{
	public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
