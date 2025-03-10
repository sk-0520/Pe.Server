<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

readonly class DatabaseInformation
{
	public function __construct(
		public string $name
	) {
		//NOP
	}
}
