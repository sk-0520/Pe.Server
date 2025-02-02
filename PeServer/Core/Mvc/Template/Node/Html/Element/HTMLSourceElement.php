<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSourceAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLSourceElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLSourceAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLSourceAttributes $attributes = new HTMLSourceAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"source",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
