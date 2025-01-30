<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLFormElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLFormAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLFormAttributes $attributes = new HTMLFormAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"form",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
