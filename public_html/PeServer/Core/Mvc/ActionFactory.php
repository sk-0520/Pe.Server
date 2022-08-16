<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;

class ActionFactory extends DiFactoryBase implements IActionFactory
{
	public function __construct(
		IDiContainer $container
	) {
		parent::__construct($container);
	}

	//[IActionFactory]

	public function new(string $actionClassName, mixed $arguments = null): Action
	{
		return $this->container->new($actionClassName);
	}
}
