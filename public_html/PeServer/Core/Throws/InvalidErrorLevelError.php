<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\CoreError;

final class InvalidErrorLevelError extends CoreError
{
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
