<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLHtmlElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHtmlAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"html",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
