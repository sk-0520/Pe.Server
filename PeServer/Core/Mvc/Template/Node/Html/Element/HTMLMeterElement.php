<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMeterAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLMeterElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLMeterAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLMeterAttributes $attributes = new HTMLMeterAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"meter",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
