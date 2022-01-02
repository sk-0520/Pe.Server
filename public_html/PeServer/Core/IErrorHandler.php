<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\Throws;
use Throwable;


interface IErrorHandler
{
	public function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void;
}
