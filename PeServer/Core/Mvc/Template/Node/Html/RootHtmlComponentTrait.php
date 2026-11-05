<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;

trait RootHtmlComponentTrait
{
	public function __construct(RootProps $props, HtmlNodeContext $context)
	{
		parent::__construct($props, $context);
	}
}
