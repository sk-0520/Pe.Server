<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSlotAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLSlotElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLSlotAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLSlotAttributes $attributes = new HTMLSlotAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"slot",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
