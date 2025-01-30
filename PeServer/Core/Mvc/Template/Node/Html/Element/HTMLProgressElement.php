<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLProgressAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLProgressElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLProgressAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLProgressAttributes $attributes = new HTMLProgressAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"progress",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
