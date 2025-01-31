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
	 * @param Props $props
	 */
	public function __construct(
		HTMLObjectAttributes $attributes = new HTMLObjectAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"object",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
