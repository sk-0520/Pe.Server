<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use stdClass;

class HTMLLinkElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLinkAttributes $attributes
	 * @param object $props
	 */
	public function __construct(
		HTMLLinkAttributes $attributes = new HTMLLinkAttributes(),
		object $props = new stdClass()
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
