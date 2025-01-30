<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLEmbedAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLEmbedElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLEmbedAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLEmbedAttributes $attributes = new HTMLEmbedAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"embed",
			$attributes,
			[],
			$props,
			HtmlElementOptions::block(true)
		);
	}
}
