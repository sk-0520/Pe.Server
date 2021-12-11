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

	protected function format(int $level, int $traceIndex, $message, ...$parameters): string
	{
		return Logging::format($level, $traceIndex + 1, $this->header, $message, ...$parameters);
	}

	/**
	 * ログ出力。
	 *
	 * @param integer $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @param mixed $message メッセージかオブジェクト。
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	protected abstract function logImpl(int $level, int $traceIndex, $message, ...$parameters): void;

	public final function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		if ($level < $this->level) {
			return;
		}

		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	public final function trace($message, ...$parameters): void
	{
		$this->log(self::LEVEL_TRACE, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function debug($message, ...$parameters): void
	{
		$this->log(self::LEVEL_DEBUG, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function info($message, ...$parameters): void
	{
		$this->log(self::LEVEL_INFORMATION, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function warn($message, ...$parameters): void
	{
		$this->log(self::LEVEL_WARNING, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function error($message, ...$parameters): void
	{
		$this->log(self::LEVEL_ERROR, $this->baseTraceIndex + 1, $message, ...$parameters);
	}
}
