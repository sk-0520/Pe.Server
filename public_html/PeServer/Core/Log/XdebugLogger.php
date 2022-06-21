<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;

/**
 * XDebugロガー。
 */
final class XdebugLogger extends LoggerBase
{
	/**
	 * 生成。
	 */
	public function __construct(string $header, int $level, int $baseTraceIndex)
	{
		parent::__construct('{TIMESTAMP} |{LEVEL}| {METHOD}: {MESSAGE} | {FILE_NAME}({LINE})', $header, $level, $baseTraceIndex);
	}

	protected final function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);
		\xdebug_notify($logMessage);
	}
}
