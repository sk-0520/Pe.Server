<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\ILogger;

abstract class LoggerBase implements ILogger
{
	protected $traceIndex = 4;

	protected $header;
	protected $level;
	protected $formatter;

	public function __construct(string $header, int $level, ?callable $formatter)
	{
		$this->header = $header;
		$this->level = $level;
		$this->formatter = $formatter;
	}

	protected abstract function logImpl(int $level, int $traceIndex, string $formattedMessage, string $message, ?array $parameters = null): void;

	public final function format(int $level, int $traceIndex, string $message, ?array $parameters = null): string
	{
		return call_user_func($this->formatter, $level, $traceIndex, $message, $parameters);
	}

	public final function log(int $level, int $traceIndex, string $message, ?array $parameters = null): void
	{
		if($this->level < $level) {
			return;
		}

		$formattedMessage = is_null($this->formatter) ? '': $this->format($level, $traceIndex, $message, $parameters);
		$this->logImpl($level, $traceIndex, $formattedMessage, $message, $parameters);
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
