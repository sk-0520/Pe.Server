<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use Attribute;

/**
 */
#[Attribute(Attribute::TARGET_CLASS)]
readonly class Version
{
	public function __construct(
		public int $version
	) {
	}
}
