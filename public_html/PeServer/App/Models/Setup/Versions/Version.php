<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use \Attribute;

/**
 * @immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Version
{
	public function __construct(
		public int $version
	) {
	}
}
