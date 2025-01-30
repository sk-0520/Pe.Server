<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLIFrameAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLIFrameElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLIFrameAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLIFrameAttributes $attributes = new HTMLIFrameAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"iframe",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
