<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use stdClass;

class HTMLHeadElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHeadAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 */
	public function __construct(
		HTMLHeadAttributes $attributes = new HTMLHeadAttributes(),
		array $children = [],
		object $props = new stdClass()
	) {
		parent::__construct("head", $attributes, $children, $props, HtmlElementOptions::block(false));
	}
}
