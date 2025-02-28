<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use DateTimeImmutable;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;

/**
 * ログ出力基底。
 *
 * @phpstan-import-type MessageAlias from ILogger
 */
abstract class LoggerBase implements ILogger
{
	/**
	 * 生成。
	 *
	 * @param LogOptions $options
	 */
	protected function __construct(
		protected readonly Logging $logging,
		protected readonly LogOptions $options
	) {
	}

	#region function

	/**
	 * ログ書式適用。
	 *
	 * @param int $level ログレベル
	 * @phpstan-param ILogger::LOG_LEVEL_* $level 有効レベル。
	 * @param int $traceIndex トレース位置。
	 * @phpstan-param non-negative-int $traceIndex
	 * @param mixed $message メッセージ。
	 * @phpstan-param MessageAlias $message
	 * @param mixed ...$parameters パラメータ。
	 * @return string
	 */
	protected function format(int $level, int $traceIndex, $message, ...$parameters): string
	{
		return $this->logging->format($this->options->format, $level, $traceIndex + 1, new DateTimeImmutable(), $this->options->header, $message, ...$parameters);
	}

	/**
	 * ログ出力実装。
	 *
	 * @param int $level ログレベル。
	 * @phpstan-param self::LOG_LEVEL_* $level ログレベル。
	 * @param int $traceIndex 現在フレーム数。
	 * @phpstan-param non-negative-int $traceIndex
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param MessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	abstract protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void;

	#endregion

	#region ILogger

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		// 有効レベル未満であれば何もしない
		if ($level < $this->options->level) {
			return;
		}

		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	final public function trace($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_TRACE, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	final public function debug($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_DEBUG, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	final public function info($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_INFORMATION, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	final public function warn($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_WARNING, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}
	final public function error($message, ...$parameters): void
	{
		$this->log(self::LOG_LEVEL_ERROR, $this->options->baseTraceIndex + 1, $message, ...$parameters);
	}

	#endregion
}
