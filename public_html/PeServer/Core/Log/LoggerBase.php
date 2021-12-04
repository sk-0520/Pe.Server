<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\ILogger;
use \PeServer\Core\Log\Logging;

abstract class LoggerBase implements ILogger
{
	protected $traceIndex = 2;

	protected $header;
	protected $level;

	public function __construct(string $header, int $level)
	{
		$this->header = $header;
		$this->level = $level;
	}

	protected abstract function logImpl(int $level, int $traceIndex, string $message, ?array $parameters = null);

	public function log(int $level, int $traceIndex, string $message, ?array $parameters = null): void
	{
		if($this->level < $level) {
			return;
		}

		$this->logImpl($level, $traceIndex, $message, $parameters);
	}

	public function trace(string $message, ?array $parameters = null): void
	{
		$this->log(self::LEVEL_TRACE, $this->traceIndex, $message, $parameters);
	}
	public function debug(string $message, ?array $parameters = null): void
	{
		$this->log(self::LEVEL_DEBUG, $this->traceIndex, $message, $parameters);
	}
	public function info(string $message, ?array $parameters = null): void
	{
		$this->log(self::LEVEL_INFO, $this->traceIndex, $message, $parameters);
	}
	public function warn(string $message, ?array $parameters = null): void
	{
		$this->log(self::LEVEL_WARN, $this->traceIndex, $message, $parameters);
	}
	public function error(string $message, ?array $parameters = null): void
	{
		$this->log(self::LEVEL_ERROR, $this->traceIndex, $message, $parameters);
	}
}
