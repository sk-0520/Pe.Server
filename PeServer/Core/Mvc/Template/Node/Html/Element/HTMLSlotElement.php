<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSlotAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLSlotElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLSlotAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLSlotAttributes $attributes = new HTMLSlotAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"slot",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
