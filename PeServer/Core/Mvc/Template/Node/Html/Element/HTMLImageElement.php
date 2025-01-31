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
	 * @param Props $props
	 */
	public function __construct(
		HTMLImageAttributes $attributes = new HTMLImageAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"img",
			$attributes,
			new NoneContent(),
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
