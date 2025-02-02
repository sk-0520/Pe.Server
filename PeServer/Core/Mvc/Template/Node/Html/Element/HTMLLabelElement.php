<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLLabelElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLabelAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLLabelAttributes $attributes = new HTMLLabelAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"label",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
