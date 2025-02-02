<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextContent;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLTitleElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes
	 * @param TextContent $content

	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
		TextContent $content = new TextContent(""),
	) {
		parent::__construct(
			"title",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
