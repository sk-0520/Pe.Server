<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLIAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLLIElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLIAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLLIAttributes $attributes = new HTMLLIAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"li",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
