<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDListElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"dl",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
