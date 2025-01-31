<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLHtmlElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHtmlAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"html",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
