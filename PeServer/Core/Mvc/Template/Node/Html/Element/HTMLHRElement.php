<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLHRElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"hr",
			$attributes,
			[],
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
