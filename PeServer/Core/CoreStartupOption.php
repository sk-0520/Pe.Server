<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Web\IUrlHelper;

readonly class CoreStartupOption
{
	public function __construct(
		public string $environment,
		public string $revision,
		public IUrlHelper|null $urlHelper,
		public SpecialStore|null $specialStore
	) {
		//NOP
	}
}
