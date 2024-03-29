<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;

trait ThrowableTrait
{
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
