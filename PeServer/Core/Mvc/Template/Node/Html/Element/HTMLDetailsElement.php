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

	 */
	public function __construct(
		HTMLDetailsAttributes $attributes = new HTMLDetailsAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"details",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
