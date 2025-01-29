<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDetailsAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDetailsElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLDetailsAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLDetailsAttributes $attributes = new HTMLDetailsAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"details",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
