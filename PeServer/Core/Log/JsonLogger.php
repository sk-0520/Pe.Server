<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use Override;

class JsonLogger extends FileLogger
{

	public function __construct(Logging $logging, LogOptions $options)
	{
		parent::__construct($logging, $options);
	}

	#[Override]
	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);
		echo $logMessage, PHP_EOL;
	}
}
