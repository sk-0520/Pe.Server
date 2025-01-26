<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Throws\NotImplementedException;

class HTMLElement extends Element
{
	/**
	 *
	 * @param string $tagName
	 * @param HtmlAttributes $attributes
	 * @param array<string,mixed> $props
	 * @param INode[] $children
	 */
	public function __construct(
		string $tagName,
		HtmlAttributes $attributes = new HtmlAttributes(),
		array $children = [],
		array $props = []
	) {
		parent::__construct($tagName, $attributes, $children, $props);
	}
}
