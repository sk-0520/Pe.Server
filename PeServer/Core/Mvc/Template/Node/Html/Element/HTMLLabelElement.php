<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLLabelElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLabelAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLLabelAttributes $attributes = new HTMLLabelAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"label",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
