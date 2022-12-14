<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Log\ILogger;

/**
 * ロガー生成時にコンストラクタに渡される設定値等。
 */
class LogOptions
{
	/**
	 * 生成。
	 *
	 * @param string $header
	 * @phpstan-param non-empty-string $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @param int $level
	 * @phpstan-param ILogger::LOG_LEVEL_* $level
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param array<string,mixed> $configuration
	 */
	public function __construct(
		public string $header,
		public int $baseTraceIndex,
		public int $level,
		public string $format,
		public array $configuration = []
	) {
	}
}
