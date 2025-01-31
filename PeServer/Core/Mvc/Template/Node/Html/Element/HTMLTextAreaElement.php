<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTextAreaAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;

class HTMLTextAreaElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTextAreaAttributes $attributes
	 * @param TextNode $child
	 * @param Props $props
	 */
	public function __construct(
		HTMLTextAreaAttributes $attributes = new HTMLTextAreaAttributes(),
		TextNode $child = new TextNode(""),
		Props $props = new Props()
	) {
		parent::__construct(
			"textarea",
			$attributes,
			[$child],
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
