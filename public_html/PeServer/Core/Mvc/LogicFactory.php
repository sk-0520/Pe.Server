<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\LoggerFactory;

class LogicFactory extends DiFactoryBase implements ILogicFactory
{
	public function __construct(IDiContainer $container)
	{
		parent::__construct($container);
	}

	//[ILogicFactory]

	public function createLogic(string $logicClassName, array $arguments = []): LogicBase
	{
		$logger = $this->container->new(LoggerFactory::class)->createLogger($logicClassName);
		$parameter = $this->container->new(LogicParameter::class, [ILogger::class => $logger]);
		$arguments[LogicParameter::class] = $parameter;
		return $this->container->new($logicClassName, $arguments);
	}
}
