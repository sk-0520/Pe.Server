<?php

declare(strict_types=1);

namespace PeServer\Core;

interface ILogger
{
	const LEVEL_TRACE = 1;
	const LEVEL_DEBUG = 2;
	const LEVEL_INFO = 3;
	const LEVEL_WARN = 4;
	const LEVEL_ERROR = 5;

	function log(int $level, int $traceIndex, string $message, ?array $parameters = null): void;
	function trace(string $message, ?array $parameters = null): void;
	function debug(string $message, ?array $parameters = null): void;
	function info(string $message, ?array $parameters = null): void;
	function warn(string $message, ?array $parameters = null): void;
	function error(string $message, ?array $parameters = null): void;
}
