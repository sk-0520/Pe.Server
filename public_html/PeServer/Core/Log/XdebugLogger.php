<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\LoggerBase;

/**
 * XDebugロガー。
 */
final class XdebugLogger extends LoggerBase
{
	public function __construct(string $header, int $level, int $baseTraceIndex)
	{
		parent::__construct('{TIME} |{LEVEL}| {METHOD}: {MESSAGE} | {FILE_NAME}({LINE})', $header, $level, $baseTraceIndex);
	}

	protected final function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		if (!function_exists('xdebug_is_debugger_active') || !\xdebug_is_debugger_active()) {
			return;
		}

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);
		\xdebug_notify($logMessage);
	}
}
