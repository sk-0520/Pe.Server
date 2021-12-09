<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\ILogger;

abstract class LoggerBase implements ILogger
{
	protected $header;
	protected $level;
	protected $baseTraceIndex;

	public function __construct(string $header, int $level, int $baseTraceIndex)
	{
		$this->header = $header;
		$this->level = $level;
		$this->baseTraceIndex = $baseTraceIndex;
	}

	protected function format(int $level, int $traceIndex, $message, string ...$parameters): string
	{
		return Logging::format($level, $traceIndex + 1, $message, ...$parameters);
	}

	/**
	 * ログ出力。
	 *
	 * @param integer $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @param mixed $message　メッセージ。
	 * @param string ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	protected abstract function logImpl(int $level, int $traceIndex, $message, string ...$parameters): void;

	public final function log(int $level, int $traceIndex, $message, string ...$parameters): void
	{
		if ($this->level < $level) {
			return;
		}

		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	public final function trace($message, string ...$parameters): void
	{
		$this->log(self::LEVEL_TRACE, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function debug($message, string ...$parameters): void
	{
		$this->log(self::LEVEL_DEBUG, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function info($message, string ...$parameters): void
	{
		$this->log(self::LEVEL_INFO, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function warn($message, string ...$parameters): void
	{
		$this->log(self::LEVEL_WARN, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function error($message, string ...$parameters): void
	{
		$this->log(self::LEVEL_ERROR, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
}
