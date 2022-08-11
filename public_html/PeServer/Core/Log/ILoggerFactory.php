<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

interface ILoggerFactory
{
	function new(string|object $header, mixed $arguments = null): ILogger;
}
