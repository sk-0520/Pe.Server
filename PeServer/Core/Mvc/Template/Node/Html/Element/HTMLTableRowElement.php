<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableRowAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableRowElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableRowAttributes $attributes
	 * @param HtmlContent $content,
	 * @param Props $props
	 */
	public function __construct(
		HTMLTableRowAttributes $attributes = new HTMLTableRowAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"tr",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
