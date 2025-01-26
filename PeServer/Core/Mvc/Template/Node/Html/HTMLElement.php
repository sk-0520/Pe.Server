<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Throws\NotImplementedException;

readonly class HTMLElement extends Element
{
	/**
	 *
	 * @param string $tagName
	 * @param array<string,string> $attributes
	 * @phpstan-param HtmlTagAttributeAlias $attributes
	 * @param array<string,mixed> $props
	 * @param INode[] $children
	 */
	public function __construct(
		string $tagName,
		array $attributes = [],
		array $children = [],
		array $props = []
	) {
		parent::__construct($tagName, $attributes, $children, $props);
	}
}
