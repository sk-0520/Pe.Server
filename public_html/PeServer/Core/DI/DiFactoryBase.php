<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

class DiFactoryBase
{
	protected function __construct(
		protected IDiContainer $container
	) {
	}
}
