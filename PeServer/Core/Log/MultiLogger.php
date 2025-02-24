<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\LoggerBase;

/**
 * 複数出力ロガー。
 */
final class MultiLogger implements ILogger
{
	/**
	 * 生成。
	 *
	 * @param int $baseTraceIndex
	 * @phpstan-param non-negative-int $baseTraceIndex
	 * @param ILogger[] $loggers ロガー一覧。
	 */
	public function __construct(
		private readonly int $baseTraceIndex,
		private readonly array $loggers
	) {
		//NOP
	}

	#region function

	/**
	 * 横流し処理。
	 *
	 * @param int $level レベル。
	 * @phpstan-param ILogger::LOG_LEVEL_* $level
	 * @param int $level
	 * @param int $traceIndex
	 * @phpstan-param non-negative-int $traceIndex
	 * @param mixed $message
	 * @phpstan-param globa-alias-log-message $message
	 * @param mixed ...$parameters
	 */
	private function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($level, $traceIndex + 1, $message, ...$parameters);
		}
	}

	#endregion

	#region ILogger

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	public function trace($message, ...$parameters): void
	{
		$this->logImpl(ILogger::LOG_LEVEL_TRACE, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public function debug($message, ...$parameters): void
	{
		$this->logImpl(ILogger::LOG_LEVEL_DEBUG, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public function info($message, ...$parameters): void
	{
		$this->logImpl(ILogger::LOG_LEVEL_INFORMATION, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public function warn($message, ...$parameters): void
	{
		$this->logImpl(ILogger::LOG_LEVEL_WARNING, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public function error($message, ...$parameters): void
	{
		$this->logImpl(ILogger::LOG_LEVEL_ERROR, $this->baseTraceIndex + 1, $message, ...$parameters);
	}

	#endregion
}
