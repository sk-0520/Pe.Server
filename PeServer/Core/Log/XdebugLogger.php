<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\LoggerBase;
use PeServer\Core\Log\LogOptions;

/**
 * XDebugロガー。
 *
 * @codeCoverageIgnore
 */
final class XdebugLogger extends LoggerBase
{
	public function __construct(Logging $logging, LogOptions $options)
	{
		parent::__construct($logging, $options);
	}

	#region LoggerBase

	final protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		if (!\xdebug_is_debugger_active()) {
			return;
		}

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);
		\xdebug_notify($logMessage);
	}

	#endregion
}
