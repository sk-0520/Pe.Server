<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class BuildAttributeBool
{
	public function __construct(
		public string $true,
		public string $false
	) {
		//NOP
	}
}
