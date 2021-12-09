<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\Log\LoggerBase;

final class MultiLogger extends LoggerBase
{
	private $loggers;

	public function __construct(string $header, int $level, int $baseTraceIndex, array $loggers)
	{
		parent::__construct($header, $level, $baseTraceIndex);
		$this->loggers = $loggers;
	}

	protected function logImpl(int $level, int $traceIndex, $message, string ...$parameters): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($level, $traceIndex + 1, $message, ...$parameters);
		}
	}
}
