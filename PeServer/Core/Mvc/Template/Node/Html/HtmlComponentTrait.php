<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Props;

trait HtmlComponentTrait
{
	protected function __construct(Props $props, HtmlBuilder $builder)
	{
		// @phpstan-ignore argument.type
		parent::__construct($props, $builder);
	}
}
