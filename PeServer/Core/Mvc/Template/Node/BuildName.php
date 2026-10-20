<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class BuildName
{
	public function __construct(
		/** @var non-empty-string */
		public readonly string $name
	) {
		//NOP
	}
}
