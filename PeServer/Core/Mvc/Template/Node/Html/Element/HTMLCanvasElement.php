<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLCanvasAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLCanvasElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLCanvasAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLCanvasAttributes $attributes = new HTMLCanvasAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"canvas",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
