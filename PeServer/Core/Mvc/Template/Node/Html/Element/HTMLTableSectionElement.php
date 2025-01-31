<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableSectionElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param "tbody"|"thead"|"tfoot" $tagName
	 * @param HtmlAttributes $attributes
	 * @param INode[] $children,
	 * @param Props $props
	 */
	public function __construct(
		string $tagName,
		HtmlAttributes $attributes = new HtmlAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			$tagName,
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
