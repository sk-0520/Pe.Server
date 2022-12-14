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
	#region variable

	/**
	 * ロガー一覧。
	 *
	 * @var ILogger[]
	 * @readonly
	 */
	private array $loggers;

	/**
	 * 基準位置。
	 *
	 * @var int
	 * @phpstan-var UnsignedIntegerAlias
	 * @readonly
	 */
	private int $baseTraceIndex;

	#endregion

	/**
	 * 生成。
	 *
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @param ILogger[] $loggers ロガー一覧。
	 */
	public function __construct(int $baseTraceIndex, array $loggers)
	{
		$this->baseTraceIndex = $baseTraceIndex;
		$this->loggers = $loggers;
	}

	#region function

	/**
	 * 横流し処理。
	 *
	 * @param integer $level レベル。
	 * @phpstan-param ILogger::LOG_LEVEL_* $level
	 * @param integer $level
	 * @param integer $traceIndex
	 * @phpstan-param UnsignedIntegerAlias $traceIndex
	 * @param mixed $message
	 * @phpstan-param LogMessageAlias $message
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
