<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLAnchorElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAnchorAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLAnchorAttributes $attributes = new HTMLAnchorAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"a",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
