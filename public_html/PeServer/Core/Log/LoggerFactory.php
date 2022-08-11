<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\DI\DiContainer;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;

class LoggerFactory extends DiFactoryBase implements ILoggerFactory
{
	public function __construct(
		DiContainer $container
	) {
		parent::__construct($container);
	}

	//[ILoggerFactory]

	public function new(string|object $header, int $baseTraceIndex = 0, mixed $arguments = null): ILogger
	{
		$h = '';
		if (is_string($header)) {
			if (Text::isNullOrWhiteSpace($header)) { //@phpstan-ignore-line
				throw new ArgumentException('$header');
			}
			$h = $header;
		} else {
			$h = TypeUtility::getType($header);
		}

		return new XdebugLogger($h, ILogger::LOG_LEVEL_TRACE, $baseTraceIndex + 1);
	}
}
