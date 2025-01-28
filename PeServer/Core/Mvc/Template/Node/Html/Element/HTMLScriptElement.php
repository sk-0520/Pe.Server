<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLScriptElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLScriptAttributes $attributes
	 * @param INode $child
	 * @param Props $props
	 */
	public function __construct(
		HTMLScriptAttributes $attributes = new HTMLScriptAttributes(),
		INode $child = new TextNode(""),
		Props $props = new Props()
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
