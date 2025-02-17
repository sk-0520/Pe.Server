<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\IDiContainer;


class ItSetup
{
	public function __construct(
		public IDiContainer $container,
		public IDatabaseContext $databaseContext,
	) {
		//NOP
	}
}
