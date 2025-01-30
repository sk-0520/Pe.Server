<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOListAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOListElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOListAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLOListAttributes $attributes = new HTMLOListAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"ol",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
