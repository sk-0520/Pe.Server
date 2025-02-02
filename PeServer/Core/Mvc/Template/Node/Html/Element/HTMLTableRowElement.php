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

	 */
	public function __construct(
		HTMLTableRowAttributes $attributes = new HTMLTableRowAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"tr",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
