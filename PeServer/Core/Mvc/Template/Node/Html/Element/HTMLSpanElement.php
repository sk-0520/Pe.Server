<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLSpanElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"span",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
