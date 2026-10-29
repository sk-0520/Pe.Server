<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Xml;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\NodeContext;
use PeServer\Core\Mvc\Template\Node\Xml\XmlBuilder;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;

/**
 * @extends NodeContext<XmlBuilder>
 */
readonly class XmlNodeContext extends NodeContext
{
	public function __construct(
		ProgramContext $programContext,
		XmlBuilder $builder,
		IUrlHelper $urlHelper,
		Stores $stores,
		Environment $environment
	) {
		parent::__construct($programContext, $builder, $urlHelper, $stores, $environment);
	}
}
