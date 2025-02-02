<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptGroupAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOptGroupElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOptGroupAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLOptGroupAttributes $attributes = new HTMLOptGroupAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"optgroup",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
