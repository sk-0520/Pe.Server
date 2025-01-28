<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLTitleElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes
	 * @param INode $child
	 * @param object $props
	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
		INode $child = new TextNode(""),
		object $props = new stdClass()
	) {
		parent::__construct(
			"title",
			$attributes,
			[$child],
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
