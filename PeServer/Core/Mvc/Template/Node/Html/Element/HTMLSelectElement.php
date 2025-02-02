<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSelectAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;

class HTMLSelectElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLSelectAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLSelectAttributes $attributes = new HTMLSelectAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"select",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
