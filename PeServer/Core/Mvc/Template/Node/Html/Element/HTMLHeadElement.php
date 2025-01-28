<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLHeadElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHeadAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLHeadAttributes $attributes = new HTMLHeadAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"head",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
