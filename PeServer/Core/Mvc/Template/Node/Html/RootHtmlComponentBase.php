<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @template TRootProps of RootProps
 * @extends ComponentBase<TRootProps>
 */
abstract class RootHtmlComponentBase extends ComponentBase
{
	/**
	 * @param TRootProps $props
	 * @param HtmlBuilder $builder
	 */
	protected function __construct(RootProps $props, readonly protected HtmlBuilder $builder)
	{
		parent::__construct($props);
	}
}
