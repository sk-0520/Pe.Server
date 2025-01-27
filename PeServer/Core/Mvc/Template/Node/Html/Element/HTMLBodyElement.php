<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use stdClass;

class HTMLBodyElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLBodyAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 */
	public function __construct(
		HTMLBodyAttributes $attributes = new HTMLBodyAttributes(),
		array $children = [],
		object $props = new stdClass()
	) {
		parent::__construct("body", $attributes, $children, $props, HtmlElementOptions::block(false));
	}
}
