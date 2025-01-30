<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLObjectAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLObjectElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLObjectAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLObjectAttributes $attributes = new HTMLObjectAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"object",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
