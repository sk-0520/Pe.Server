<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLIFrameAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLIFrameElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLIFrameAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLIFrameAttributes $attributes = new HTMLIFrameAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"iframe",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
