<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @template TRootProps of RootProps
 * @extends HtmlComponentBase<TRootProps>
 */
abstract class RootHtmlComponentBase extends HtmlComponentBase
{
	/**
	 * @param TRootProps $props
	 * @param HtmlNodeContext $context
	 */
	protected function __construct(RootProps $props, HtmlNodeContext $context)
	{
		parent::__construct($props, $context);
	}
}
