<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

interface ILogProvider
{
	/**
	 * 指定したログの破棄。
	 *
	 * @param string $name
	 * @return bool
	 */
	function clear(string $name): bool;

	function clearAll(): void;

	/**
	 * 追加。
	 *
	 * @param string $name
	 * @param string $logger
	 * @phpstan-param class-string<ILogger> $logger
	 * @param array{level:int,format:string,logger?:array<string,mixed>|null} $configuration
	 * @phpstan-param array{level:ILogger::LOG_LEVEL_*,format:literal-string,logger?:array<string,mixed>|null} $configuration
	 */
	function add(string $name, string $logger, array $configuration): void;

	/**
	 * ロガーの生成。
	 *
	 * @param string $header
	 * @phpstan-param non-empty-string $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @return ILogger[]
	 */
	function create(string $header, int $baseTraceIndex): array;
}
