<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLVideoAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLVideoElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLVideoAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLVideoAttributes $attributes = new HTMLVideoAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"video",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
