<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\Log\LoggerBase;

class FileLogger extends LoggerBase
{
	public function __construct(string $header, int $level)
	{
		parent::__construct($header, $level);
	}

	protected function logImpl(int $level, int $traceIndex, string $message, ?array $parameters = null)
	{

	}
}
