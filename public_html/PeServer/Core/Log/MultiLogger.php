<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\Log\LoggerBase;

final class MultiLogger extends LoggerBase
{
	private $loggers;

	public function __construct(string $header, int $level, array $loggers)
	{
		parent::__construct($header, $level);
		$this->loggers = $loggers;
	}

	protected function logImpl(int $level, int $traceIndex, string $formattedMessage, string $message, ?array $parameters = null)
	{
		foreach($this->loggers as $logger) {
			$logger->log($level, $traceIndex, $message, $parameters);
		}
	}
}
