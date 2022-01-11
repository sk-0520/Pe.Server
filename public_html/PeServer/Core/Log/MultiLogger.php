<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ILogger;
use PeServer\Core\Log\LoggerBase;

final class MultiLogger extends LoggerBase
{
	/**
	 * ロガー一覧。
	 *
	 * @var ILogger[]
	 */
	private array $loggers;

	/**
	 * 生成。
	 *
	 * @param string $header
	 * @param integer $level
	 * @param integer $baseTraceIndex
	 * @param ILogger[] $loggers
	 */
	public function __construct(string $header, int $level, int $baseTraceIndex, array $loggers)
	{
		parent::__construct($header, $level, $baseTraceIndex);
		$this->loggers = $loggers;
	}

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	/**
	 * ログ出力実装。
	 *
	 * @param integer $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @param mixed $message メッセージかオブジェクト。
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($level, $traceIndex + 1, $message, ...$parameters);
		}
	}
}
