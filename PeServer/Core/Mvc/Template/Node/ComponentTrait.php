<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Props;

/**
 * @template TProps of Props
 * @phpstan-ignore trait.unused
 */
trait ComponentTrait
{
	/**
	 * @phpstan-param TProps $props
	 */
	protected function __construct(Props $props)
	{
		parent::__construct($props);
	}
}
