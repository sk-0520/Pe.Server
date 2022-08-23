<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;

class TransactionException extends DatabaseException
{
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}