<?php

declare(strict_types=1);

namespace PeServer\Core;

class Logger implements ILogger
{
	protected $traceIndex = 2;

	protected $header;
	protected $loggingConfiguration;

	public function __construct(string $header, array $loggingConfiguration)
	{
		$this->header = $header;
		$this->loggingConfiguration = $loggingConfiguration;
	}

	private function log(int $level, int $traceIndex, string $message, ?array $parameters = null)
	{
		if($this->loggingConfiguration['level'] < $level) {
			return;
		}
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
