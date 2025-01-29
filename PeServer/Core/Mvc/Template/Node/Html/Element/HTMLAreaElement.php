<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLAreaElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAreaAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLAreaAttributes $attributes = new HTMLAreaAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"area",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
