<?php

declare(strict_types=1);

namespace PeServer\Core\Setup;

use PeServer\Core\Database\IDatabaseContext;

class MigrationArgument
{
	public function __construct(
		public IDatabaseContext $context
	) {
		//NOP
	}
}
