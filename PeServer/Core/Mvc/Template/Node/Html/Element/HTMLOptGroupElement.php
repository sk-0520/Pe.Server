<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptGroupAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOptGroupElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOptGroupAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLOptGroupAttributes $attributes = new HTMLOptGroupAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"optgroup",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
