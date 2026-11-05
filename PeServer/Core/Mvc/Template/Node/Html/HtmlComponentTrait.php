<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;

/**
 * @template TProps of \PeServer\Core\Mvc\Template\Node\Props
 */
trait HtmlComponentTrait
{
	/**
	 * @param TProps $props
	 * @param HtmlNodeContext $context
	 */
	public function __construct(Props $props, HtmlNodeContext $context)
	{
		parent::__construct($props, $context);
	}
}
