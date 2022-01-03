<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\HttpStatus;
use \Throwable;
use PeServer\Core\Throws;

final class HttpStatusException extends CoreException
{
	public HttpStatus $status;

	public function __construct(HttpStatus $status, string $message = "", ?Throwable $previous = null)
	{
		$this->status = $status;

		parent::__construct($message, $status->code(), $previous);
	}
}
