<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableAttributes $attributes
	 * @param HtmlContent $content,
	 * @param Props $props
	 */
	public function __construct(
		HTMLTableAttributes $attributes = new HTMLTableAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"table",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
