<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILogProvider;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;

class LogProvider implements ILogProvider
{
	/**
	 * ロガー。
	 *
	 * @var array<string,LocalLogProviderItem>
	 */
	private array $loggers = [];

	//[ILogProvider]

	public function clear(string $name): bool
	{
		if (isset($this->loggers[$name])) {
			unset($this->loggers[$name]);
			return true;
		}

		return false;
	}

	public function clearAll(): void
	{
		$this->loggers = [];
	}

	public function add(string $name, string $logger, int $level, string $format, array $configuration): void
	{
		if (isset($this->loggers[$name])) {
			throw new ArgumentException('$name: ' . $name);
		}

		$this->loggers[$name] = new LocalLogProviderItem(
			$logger,
			$level,
			$format,
			$configuration
		);
	}

	public function create(string $header, int $baseTraceIndex): array
	{
		if (empty($this->loggers)) {
			return [];
		}

		$result = [];

		foreach ($this->loggers as $item) {
			$options = new LogOptions($header, $baseTraceIndex, $item->level, $item->format, $item->configuration);
			$result[] = ReflectionUtility::create($item->loggerClass, ILogger::class, $options);
		}

		return $result;
	}
}

/**
 * `LogProvider` 内で持ち運ぶロガー設定。
 *
 * @immutable
 */
class LocalLogProviderItem
{
	/**
	 * 生成
	 *
	 * @param string $loggerClass
	 * @phpstan-param class-string<ILogger> $loggerClass
	 * @param int $level
	 * @phpstan-param ILogger::LOG_LEVEL_* $level
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param array<string,mixed> $configuration
	 */
	public function __construct(
		public string $loggerClass,
		public int $level,
		public string $format,
		public array $configuration
	) {
	}
}
