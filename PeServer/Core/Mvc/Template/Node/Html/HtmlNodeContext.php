<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\NodeContextBase;
use PeServer\Core\ProgramContext;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Store\Stores;
use PeServer\Core\Environment;

readonly class HtmlNodeContext extends NodeContextBase
{
	public function __construct(
		ProgramContext $programContext,
		IUrlHelper $urlHelper,
		Stores $stores,
		Environment $environment,
		public WebSecurity $webSecurity,
		public HtmlBuilder $builder
	) {
		parent::__construct($programContext, $urlHelper, $stores, $environment);
	}
}
