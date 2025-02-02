<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableAttributes $attributes
	 * @param HtmlContent $content,

	 */
	public function __construct(
		HTMLTableAttributes $attributes = new HTMLTableAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"table",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
