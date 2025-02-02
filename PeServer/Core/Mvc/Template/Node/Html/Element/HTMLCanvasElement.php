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

	 */
	public function __construct(
		HTMLCanvasAttributes $attributes = new HTMLCanvasAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"canvas",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
