<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableAttributes $attributes
	 * @param INode[] $children,
	 * @param Props $props
	 */
	public function __construct(
		HTMLTableAttributes $attributes = new HTMLTableAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"table",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
