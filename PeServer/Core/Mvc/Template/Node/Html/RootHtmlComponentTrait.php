<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\RootProps;

trait RootHtmlComponentTrait
{
	/**
	 * @param RootProps $props
	 * @param HtmlBuilder $builder
	 */
	protected function __construct(RootProps $props, HtmlBuilder $builder)
	{
		parent::__construct($props, $builder);
	}
}
