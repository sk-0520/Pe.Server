<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBaseAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use stdClass;

class HTMLBaseElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLBaseAttributes $attributes
	 * @param object $props
	 */
	public function __construct(
		HTMLBaseAttributes $attributes = new HTMLBaseAttributes(),
		object $props = new stdClass()
	) {
		parent::__construct("base", $attributes, [], $props, HtmlElementOptions::inline(true));
	}
}
