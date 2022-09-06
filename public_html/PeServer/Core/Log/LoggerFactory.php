<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\DI\DiContainer;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\MultiLogger;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Log\XdebugLogger;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;

class LoggerFactory extends DiFactoryBase implements ILoggerFactory
{
	public function __construct(
		IDiContainer $container
	) {
		parent::__construct($container);
	}

	#region function

	/**
	 * Xdebug出力用ロガー生成。
	 *
	 * @param string $header
	 * @phpstan-param non-empty-string $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @return XdebugLogger|null
	 */
	private function createXdebugLogger(string $header, int $baseTraceIndex): ?XdebugLogger
	{
		if (function_exists('xdebug_is_debugger_active') && \xdebug_is_debugger_active()) {
			$options = new LogOptions(
				$header,
				$baseTraceIndex,
				ILogger::LOG_LEVEL_TRACE,
				'{TIME} |{LEVEL}| <{HEADER}> {METHOD}: {MESSAGE} | {FILE_NAME}({LINE})',
				[]
			);
			return new XdebugLogger($options);
		}

		return null;
	}

	#endregion

	#region ILoggerFactory

	public function createLogger(string|object $header, int $baseTraceIndex = 0): ILogger
	{
		/** @var ILogProvider */
		$logProvider = $this->container->get(ILogProvider::class);

		$useHeader = Logging::toHeader($header);
		$loggers = $logProvider->create($useHeader, $baseTraceIndex);

		$debugLogger = $this->createXdebugLogger($useHeader, $baseTraceIndex);

		if (empty($loggers)) {
			if (is_null($debugLogger)) {
				return new NullLogger();
			}
			return $debugLogger;
		}

		if ($debugLogger !== null) {
			$loggers[] = $debugLogger;
		}

		return new MultiLogger($baseTraceIndex, $loggers);
	}

	#endregion
}
