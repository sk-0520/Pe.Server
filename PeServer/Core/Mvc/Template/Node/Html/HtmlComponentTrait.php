<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Props;

/**
 * @template TProps of \PeServer\Core\Mvc\Template\Node\Props
 */
trait HtmlComponentTrait
{
	/**
	 * @param TProps $props
	 * @param HtmlBuilder $builder
	 */
	protected function __construct(Props $props, HtmlBuilder $builder)
	{
		// @phpstan-ignore argument.type
		parent::__construct($props, $builder);
	}
}
