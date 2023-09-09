<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;

class AppEraser
{
	public function __construct(
		private AppConfiguration $config
	) {
		//NOP
	}

	#region function

	public function execute() : void {
	}

	#endregion
}
