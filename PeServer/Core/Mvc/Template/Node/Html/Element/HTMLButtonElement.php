<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLButtonElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLButtonAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLButtonAttributes $attributes = new HTMLButtonAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"button",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
