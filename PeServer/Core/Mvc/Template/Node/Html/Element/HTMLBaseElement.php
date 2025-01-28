<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBaseAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLBaseElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLBaseAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLBaseAttributes $attributes = new HTMLBaseAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"base",
			$attributes,
			[],
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
