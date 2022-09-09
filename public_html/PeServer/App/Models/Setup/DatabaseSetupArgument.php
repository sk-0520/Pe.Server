<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup;

use PeServer\Core\Database\IDatabaseContext;

class DatabaseSetupArgument
{
	public function __construct(
		public IDatabaseContext $default
	) {
	}
}
