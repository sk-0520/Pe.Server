<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\DI\DiContainer;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Throws\NotImplementedException;

class LoggerFactory extends DiFactoryBase implements ILoggerFactory
{
	public function __construct(
		DiContainer $container
	) {
		parent::__construct($container);
	}

	//[ILoggerFactory]

	public function new(string|object $header, mixed $arguments = null): ILogger
	{
		throw new NotImplementedException();
	}
}
