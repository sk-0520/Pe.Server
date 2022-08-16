<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;

/**
 * ログ出力基盤。
 */
abstract class LoggerBase implements ILogger
{
	/**
	 * 生成。
	 *
	 * @param LogOptions $options
	 */
	protected function __construct(
		/** @readonly */
		protected LogOptions $options
	) {
	}

	/**
	 * ログ書式適用。
	 *
	 * @param integer $level ログレベル
	 * @phpstan-param ILogger::LOG_LEVEL_* $level 有効レベル。
	 * @param integer $traceIndex トレース位置。
	 * @phpstan-param UnsignedIntegerAlias $traceIndex
	 * @param mixed $message メッセージ。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ。
	 * @return string
	 */
	protected function format(int $level, int $traceIndex, $message, ...$parameters): string
	{
		return Logging::format($this->options->format, $level, $traceIndex + 1, $this->options->header, $message, ...$parameters);
	}

	/**
	 * ログ出力実装。
	 *
	 * @param integer $level ログレベル。
	 * @phpstan-param self::LOG_LEVEL_* $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @phpstan-param UnsignedIntegerAlias $traceIndex
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	protected abstract function logImpl(int $level, int $traceIndex, $message, ...$parameters): void;

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		// 有効レベル未満であれば何もしない
		if ($level < $this->options->level) {
			return;
		}

		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	public final function trace($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_TRACE, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function debug($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_DEBUG, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function info($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_INFORMATION, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function warn($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_WARNING, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	public final function error($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_ERROR, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
}
