<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLBrElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLAttributes $attributes = new HTMLAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"br",
			$attributes,
			new NoneContent(),
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
