<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableColGroupElement extends HTMLElement implements IHTMLTableColElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableColAttributes $attributes
	 * @param INode[] $children,
	 * @param Props $props
	 */
	public function __construct(
		HTMLTableColAttributes $attributes = new HTMLTableColAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"colgroup",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
