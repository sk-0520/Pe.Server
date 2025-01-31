<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTextAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextContent;
use PeServer\Core\Mvc\Template\Node\TextNode;

class HTMLTextAreaElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTextAreaAttributes $attributes
	 * @param TextContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLTextAreaAttributes $attributes = new HTMLTextAreaAttributes(),
		TextContent $content = new TextContent(""),
		Props $props = new Props()
	) {
		parent::__construct(
			"textarea",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
