<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;

class HTMLElement extends Element
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes
	 * @param INode[] $children
	 * @param array<string,mixed> $props
	 * @param HtmlElementOptions $options
	 */
	protected function __construct(
		string $tagName,
		HtmlAttributes $attributes,
		array $children,
		array $props,
		HtmlElementOptions $options
    ) {
		parent::__construct($tagName, $attributes, $children, $props, $options);
	}
}
