<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLObjectAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLObjectElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLObjectAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLObjectAttributes $attributes = new HTMLObjectAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"object",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
