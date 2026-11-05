<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Environment;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;

readonly abstract class NodeContextBase
{
	public function __construct(
		public ProgramContext $program,
		public IUrlHelper $urlHelper,
		public Stores $stores,
		public Environment $environment
	) {
		//NOP
	}
}
