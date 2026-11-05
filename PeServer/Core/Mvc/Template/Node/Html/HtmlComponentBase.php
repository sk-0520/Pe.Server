<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\Props;

/**
 * @template TProps of Props
 * @extends ComponentBase<TProps>
 */
abstract class HtmlComponentBase extends ComponentBase
{
	/**
	 * @phpstan-param TProps $props
	 */
	protected function __construct(Props $props, protected readonly HtmlNodeContext $context)
	{
		parent::__construct($props);
	}
}
