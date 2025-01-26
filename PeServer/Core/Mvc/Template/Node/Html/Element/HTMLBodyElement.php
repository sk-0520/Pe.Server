<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\INode;

class HTMLBodyElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLBodyAttributes $attributes
	 * @param array<string,mixed> $props
	 * @param INode[] $children
	 */
	public function __construct(
		HTMLBodyAttributes $attributes = new HTMLBodyAttributes(),
		array $children = [],
		array $props = []
	) {
		parent::__construct("body", $attributes, $children, $props, HtmlElementOptions::block());
	}
}
