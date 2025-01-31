<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLUListAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLUListElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLUListAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLUListAttributes $attributes = new HTMLUListAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"ul",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
