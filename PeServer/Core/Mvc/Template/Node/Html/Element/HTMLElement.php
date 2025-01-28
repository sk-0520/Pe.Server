<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLElement extends Element
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $tagName
	 * @param HtmlAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @param HtmlElementOptions $options
	 */
	public function __construct(
		string $tagName,
		HtmlAttributes $attributes,
		array $children,
		Props $props,
		HtmlElementOptions $options
	) {
		parent::__construct($tagName, $attributes, $children, $props, $options);
	}
}
