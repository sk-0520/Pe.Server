<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;

class ControllerFactory extends DiFactoryBase implements IControllerFactory
{
	public function __construct(
		IDiContainer $container
	) {
		parent::__construct($container);
	}

	//[IControllerFactory]

	public function new(string $controllerClassName, mixed $arguments): ControllerBase
	{
		return $this->container->get($controllerClassName);
	}
}
