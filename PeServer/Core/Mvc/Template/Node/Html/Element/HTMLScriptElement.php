<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextContent;

class HTMLScriptElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLScriptAttributes $attributes
	 * @param TextContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLScriptAttributes $attributes = new HTMLScriptAttributes(),
		TextContent $content = new TextContent(""),
		Props $props = new Props()
	) {
		parent::__construct(
			"script",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
