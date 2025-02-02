<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLIAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLLIElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLIAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLLIAttributes $attributes = new HTMLLIAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"li",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
