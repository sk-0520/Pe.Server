<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextContent;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLStyleElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLStyleAttributes $attributes
	 * @param TextContent $content

	 */
	public function __construct(
		HTMLStyleAttributes $attributes = new HTMLStyleAttributes(),
		TextContent $content = new TextContent(""),
	) {
		parent::__construct(
			"style",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
