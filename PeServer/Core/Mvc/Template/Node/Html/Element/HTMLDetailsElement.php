<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDetailsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDetailsElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLDetailsAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLDetailsAttributes $attributes = new HTMLDetailsAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"details",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
