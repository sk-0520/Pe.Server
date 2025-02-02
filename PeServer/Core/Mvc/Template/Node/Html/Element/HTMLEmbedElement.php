<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLEmbedAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLEmbedElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLEmbedAttributes $attributes

	 */
	public function __construct(
		HTMLEmbedAttributes $attributes = new HTMLEmbedAttributes(),
	) {
		parent::__construct(
			"embed",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::block(true)
		);
	}
}
