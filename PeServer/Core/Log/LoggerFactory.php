<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\DI\DiContainer;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Environment;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\ILogProvider;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\LogOptions;
use PeServer\Core\Log\MultiLogger;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Log\XdebugLogger;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;

class LoggerFactory extends DiFactoryBase implements ILoggerFactory
{
	use DiFactoryTrait;

	#region ILoggerFactory

	public function createLogger(string|object $header, int $baseTraceIndex = 0): ILogger
	{
		/** @var ILogProvider */
		$logProvider = $this->container->get(ILogProvider::class);

		$useHeader = Logging::toHeader($header);
		$loggers = $logProvider->create($useHeader, $baseTraceIndex);

		// $debugLogger = $this->createXdebugLogger($useHeader, $baseTraceIndex);

		// if (empty($loggers)) {
		// 	if ($debugLogger === null) {
		// 		return new NullLogger();
		// 	}
		// 	return $debugLogger;
		// }

		// if ($debugLogger !== null) {
		// 	$loggers[] = $debugLogger;
		// }

		return new MultiLogger($baseTraceIndex, $loggers);
	}

	#region function

	public static function createNullFactory(): ILoggerFactory
	{
		//phpcs:ignore PSR12.Classes.AnonClassDeclaration.SpaceAfterKeyword
		return new class() implements ILoggerFactory
		{
			public function createLogger(string|object $header, int $baseTraceIndex = 0): ILogger
			{
				return new NullLogger();
			}
		};
	}

	#endregion
}
