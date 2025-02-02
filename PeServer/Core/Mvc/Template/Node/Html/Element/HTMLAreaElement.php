<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLAreaElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAreaAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLAreaAttributes $attributes = new HTMLAreaAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"area",
			$attributes,
			$content,
			HtmlElementOptions::inline(true)
		);
	}
}
