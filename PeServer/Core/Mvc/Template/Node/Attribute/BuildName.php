<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class BuildName
{
	public function __construct(
		/** @var non-empty-string */
		public string $name
	) {
		//NOP
	}
}
