<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\NodeBuilder;
use PeServer\Core\Mvc\Template\Node\Xml\XmlBuilder;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;

/**
 * @template TNodeBuilder of NodeBuilder
 */
readonly class NodeContext
{
	public function __construct(
		public ProgramContext $programContext,
		/** @var TNodeBuilder $builder */
		public NodeBuilder $builder,
		public IUrlHelper $urlHelper,
		public Stores $stores,
		public Environment $environment
	) {
	}
}
