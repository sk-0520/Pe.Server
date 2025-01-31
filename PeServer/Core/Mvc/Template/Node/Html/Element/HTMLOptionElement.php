<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptionAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOptionElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOptionAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLOptionAttributes $attributes = new HTMLOptionAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"option",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
