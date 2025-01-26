<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;

class HTMLHtmlElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHtmlAttributes $attributes
	 * @param array<string,mixed> $props
	 * @param INode[] $children
	 */
	public function __construct(
		HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(),
		array $children = [],
		array $props = []
	) {
		parent::__construct("html", $attributes, $children, $props, HtmlElementOptions::block());
	}
}
