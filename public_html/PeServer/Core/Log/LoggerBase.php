<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;


abstract class LoggerBase implements ILogger
{
	/**
	 * 生成。
	 *
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param string $header ヘッダ。使用用途により意味合いは変わるので実装側でルール決めして使用すること。
	 * @param integer $level 有効レベル。
	 * @phpstan-param ILogger::LEVEL_* $level 有効レベル。
	 * @param integer $baseTraceIndex 基準トレース位置。
	 */
	public function __construct(
		/** @readonly */
		protected string $format,
		/** @readonly */
		protected string $header,
		/** @readonly */
		protected int $level,
		/** @readonly */
		protected int $baseTraceIndex
	) {
	}

	/**
	 * ログ書式適用。
	 *
	 * @param integer $level ログレベル
	 * @phpstan-param ILogger::LEVEL_* $level 有効レベル。
	 * @param integer $traceIndex トレース位置。
	 * @param mixed $message メッセージ。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ。
	 * @return string
	 */
	protected function format(int $level, int $traceIndex, $message, ...$parameters): string
	{
		return Logging::format($this->format, $level, $traceIndex + 1, $this->header, $message, ...$parameters);
	}

	/**
	 * ログ出力実装。
	 *
	 * @param integer $level ログレベル。
	 * @phpstan-param self::LEVEL_* $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	protected abstract function logImpl(int $level, int $traceIndex, $message, ...$parameters): void;

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
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
