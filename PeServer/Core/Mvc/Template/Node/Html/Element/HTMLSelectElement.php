<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSelectAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;

class HTMLSelectElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLSelectAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLSelectAttributes $attributes = new HTMLSelectAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"select",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
