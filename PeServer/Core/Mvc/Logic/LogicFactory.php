<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Logic;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Mvc\Logic\LogicParameter;

class LogicFactory extends DiFactoryBase implements ILogicFactory
{
	use DiFactoryTrait;

	#region ILogicFactory

	public function createLogic(string $logicClassName, array $arguments = []): LogicBase
	{
		$logger = $this->container->new(LoggerFactory::class)->createLogger($logicClassName);
		$parameter = $this->container->new(LogicParameter::class, [ILogger::class => $logger]);
		$arguments[LogicParameter::class] = $parameter;
		return $this->container->new($logicClassName, $arguments);
	}

	#endregion
}
