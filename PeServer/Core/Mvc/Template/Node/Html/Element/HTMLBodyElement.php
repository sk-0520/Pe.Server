<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLBodyElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLBodyAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLBodyAttributes $attributes = new HTMLBodyAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"body",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
