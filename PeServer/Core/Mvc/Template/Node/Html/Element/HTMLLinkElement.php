<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLLinkElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLinkAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLLinkAttributes $attributes = new HTMLLinkAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"link",
			$attributes,
			[],
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
