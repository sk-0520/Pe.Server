<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ILogger;
use PeServer\Core\Log\LoggerBase;

/**
 * 複数出力ロガー。
 */
final class MultiLogger extends LoggerBase
{
	/**
	 * ロガー一覧。
	 *
	 * @var ILogger[]
	 * @readonly
	 */
	private array $loggers;

	/**
	 * 生成。
	 *
	 * @param string $header ヘッダ。使用用途により意味合いは変わるので実装側でルール決めして使用すること。
	 * @param integer $level 有効レベル。
	 * @phpstan-param self::LEVEL_* $level 有効レベル。
	 * @param integer $baseTraceIndex 基準トレース位置。
	 * @param ILogger[] $loggers ロガー一覧。
	 */
	public function __construct(string $header, int $level, int $baseTraceIndex, array $loggers)
	{
		parent::__construct('', $header, $level, $baseTraceIndex);
		$this->loggers = $loggers;
	}

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$this->logImpl($level, $traceIndex + 1, $message, ...$parameters);
	}

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($level, $traceIndex + 1, $message, ...$parameters);
		}
	}
}
