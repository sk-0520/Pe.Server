<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptionAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOptionElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOptionAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLOptionAttributes $attributes = new HTMLOptionAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"option",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
