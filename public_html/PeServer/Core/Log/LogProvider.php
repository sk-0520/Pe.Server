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
	 * @var array<array{logger_class:string,configuration:array{level:int,format:string,logger?:array<string,mixed>|null}}>
	 * @phpstan-var array<array{logger_class:class-string<ILogger>,configuration:array{level:ILogger::LOG_LEVEL_*,format:literal-string,logger?:array<string,mixed>|null}}>
	 *
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

	public function add(string $name, string $logger, array $configuration): void
	{
		if (isset($this->loggers[$name])) {
			throw new ArgumentException('$name: ' . $name);
		}

		$this->loggers[$name] = [
			'logger_class' => $logger,
			'configuration' => $configuration
		];
	}

	public function create(string $header, int $baseTraceIndex): array
	{
		if (empty($this->loggers)) {
			return [];
		}

		$result = [];

		foreach ($this->loggers as $item) {
			$configuration = $item['configuration'];
			$options = new LogOptions($header, $baseTraceIndex, $configuration);
			$result[] = ReflectionUtility::create($item['logger_class'], ILogger::class, $options);
		}

		return $result;
	}
}
