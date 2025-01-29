<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDataAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDataElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLDataAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLDataAttributes $attributes = new HTMLDataAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"data",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
