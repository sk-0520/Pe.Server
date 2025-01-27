<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLStyleElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLStyleAttributes $attributes
	 * @param INode $child
	 * @param object $props
	 */
	public function __construct(
		HTMLStyleAttributes $attributes = new HTMLStyleAttributes(),
		INode $child = new TextNode(""),
		object $props = new stdClass()
	) {
		parent::__construct("style", $attributes, [$child], $props, HtmlElementOptions::block(false));
	}
}
