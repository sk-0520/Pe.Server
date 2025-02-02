<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDataElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLDataAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLDataAttributes $attributes = new HTMLDataAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"data",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
