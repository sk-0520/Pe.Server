<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\ILogger;

interface ILogProvider
{
	#region function

	/**
	 * 指定したログの破棄。
	 *
	 * @param string $name
	 * @return bool
	 */
	public function clear(string $name): bool;

	public function clearAll(): void;

	/**
	 * 追加。
	 *
	 * @param string $name
	 * @param class-string<ILogger> $logger
	 * @param int $level
	 * @phpstan-param ILogger::LOG_LEVEL_* $level
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param array<string,mixed> $configuration
	 */
	public function add(string $name, string $logger, int $level, string $format, array $configuration): void;

	/**
	 * ロガーの生成。
	 *
	 * @param non-empty-string $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @return ILogger[]
	 */
	public function create(string $header, int $baseTraceIndex): array;

	#endregion
}
