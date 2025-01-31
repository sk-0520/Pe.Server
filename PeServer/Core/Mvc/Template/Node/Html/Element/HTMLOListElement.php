<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOListAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOListElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOListAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLOListAttributes $attributes = new HTMLOListAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"ol",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
