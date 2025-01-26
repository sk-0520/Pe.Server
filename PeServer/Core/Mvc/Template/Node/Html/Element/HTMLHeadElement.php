<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\INode;

class HTMLHeadElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHeadAttributes $attributes
	 * @param array<string,mixed> $props
	 * @param INode[] $children
	 */
	public function __construct(
		HTMLHeadAttributes $attributes = new HTMLHeadAttributes(),
		array $children = [],
		array $props = []
	) {
		parent::__construct("head", $attributes, $children, $props, HtmlElementOptions::block());
	}
}
