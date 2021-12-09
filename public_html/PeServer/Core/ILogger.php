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

	function log(int $level, int $traceIndex, $message, string ...$parameters): void;
	function trace($message, string ...$parameters): void;
	function debug($message, string ...$parameters): void;
	function info($message, string ...$parameters): void;
	function warn($message, string ...$parameters): void;
	function error($message, string ...$parameters): void;
}
