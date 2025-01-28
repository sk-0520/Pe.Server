<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLScriptElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLScriptAttributes $attributes
	 * @param INode $child
	 * @param object $props
	 */
	public function __construct(
		HTMLScriptAttributes $attributes = new HTMLScriptAttributes(),
		INode $child = new TextNode(""),
		object $props = new stdClass()
	) {
		parent::__construct(
			"script",
			$attributes,
			[$child],
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
