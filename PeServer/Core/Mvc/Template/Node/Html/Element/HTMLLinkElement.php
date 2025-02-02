<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLLinkElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLLinkAttributes $attributes

	 */
	public function __construct(
		HTMLLinkAttributes $attributes = new HTMLLinkAttributes(),
	) {
		parent::__construct(
			"link",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::inline(true)
		);
	}
}
