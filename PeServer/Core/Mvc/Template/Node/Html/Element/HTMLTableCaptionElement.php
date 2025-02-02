<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableCaptionElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"caption",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
