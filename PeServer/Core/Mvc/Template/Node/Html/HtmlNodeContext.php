<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\NodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Store\Stores;
use PeServer\Core\Environment;

/**
 * @extends NodeContext<HtmlBuilder>
 */
readonly class HtmlNodeContext extends NodeContext
{
	public function __construct(
		ProgramContext $programContext,
		HtmlBuilder $builder,
		IUrlHelper $urlHelper,
		public WebSecurity $webSecurity,
		Stores $stores,
		Environment $environment
	) {
		parent::__construct($programContext, $builder, $urlHelper, $stores, $environment);
	}
}
