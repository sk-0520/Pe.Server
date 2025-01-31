<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLButtonElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLButtonAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLButtonAttributes $attributes = new HTMLButtonAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"button",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
