<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLQuoteAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLQuoteElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLQuoteAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLQuoteAttributes $attributes = new HTMLQuoteAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"blockquote",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
