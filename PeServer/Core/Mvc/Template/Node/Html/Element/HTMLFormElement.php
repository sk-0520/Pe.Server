<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLFormElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLFormAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLFormAttributes $attributes = new HTMLFormAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"form",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
