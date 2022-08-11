<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

interface ILoggerFactory
{
	/**
	 * Undocumented function
	 *
	 * @param string|object $header
	 * @phpstan-param non-empty-string|object $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @param mixed $arguments
	 * @return ILogger
	 */
	function new(string|object $header, int $baseTraceIndex = 0, mixed $arguments = null): ILogger;
}
