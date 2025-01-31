<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOutputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLOutputElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLOutputAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLOutputAttributes $attributes = new HTMLOutputAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"output",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
