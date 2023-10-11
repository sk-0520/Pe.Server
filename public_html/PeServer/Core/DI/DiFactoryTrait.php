<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\IDiContainer;

trait DiFactoryTrait
{
	public function __construct(IDiContainer $container)
	{
		parent::__construct($container);
	}
}
