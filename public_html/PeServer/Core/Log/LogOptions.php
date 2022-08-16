<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Log\ILogger;

/**
 * ロガー生成時にコンストラクタに渡される設定値等。
 */
class LogOptions
{
	/**
	 * ログレベル。
	 *
	 * @var int
	 * @phpstan-var ILogger::LOG_LEVEL_*
	 */
	public int $level;
	/**
	 * 書式。
	 *
	 * @var string
	 * @phpstan-var literal-string
	 */
	public string $format;
	/**
	 * ロガー専用設定。
	 *
	 * @var array<string,mixed>
	 */
	public array $logger;

	/**
	 * 生成。
	 *
	 * @param string $header
	 * @phpstan-param non-empty-string $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @param array{level:int,format:string,logger?:array<string,mixed>|null} $configuration
	 * @phpstan-param array{level:ILogger::LOG_LEVEL_*,format:literal-string,logger?:array<string,mixed>|null} $configuration
	 */
	public function __construct(
		public string $header,
		public int $baseTraceIndex,
		array $configuration
	) {
		$this->level = $configuration['level'];
		$this->format = $configuration['format'];
		$this->logger = ArrayUtility::getOr($configuration, 'logger', []); //@phpstan-ignore-line さすがにもういいよ
	}
}
