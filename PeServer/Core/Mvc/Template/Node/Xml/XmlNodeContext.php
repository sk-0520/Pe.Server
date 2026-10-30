<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Xml;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\NodeContextBase;
use PeServer\Core\Mvc\Template\Node\Xml\XmlBuilder;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;

readonly class XmlNodeContext extends NodeContextBase
{
	public function __construct(
		ProgramContext $programContext,
		IUrlHelper $urlHelper,
		Stores $stores,
		Environment $environment,
		public XmlBuilder $builder,
	) {
		parent::__construct($programContext, $urlHelper, $stores, $environment);
	}
}
