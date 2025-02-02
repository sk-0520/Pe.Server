<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLQuoteAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLQuoteElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param bool $short
	 * @param HTMLQuoteAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		bool $short,
		HTMLQuoteAttributes $attributes = new HTMLQuoteAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			$short ? "q" : "blockquote",
			$attributes,
			$content,
			new HtmlElementOptions($short, false)
		);
	}
}
