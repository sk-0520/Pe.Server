<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFieldSetAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLFieldSetElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLFieldSetAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLFieldSetAttributes $attributes = new HTMLFieldSetAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"fieldset",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
