<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImageAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLImageElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLImageAttributes $attributes

	 */
	public function __construct(
		HTMLImageAttributes $attributes = new HTMLImageAttributes(),
	) {
		parent::__construct(
			"img",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::inline(true)
		);
	}
}
