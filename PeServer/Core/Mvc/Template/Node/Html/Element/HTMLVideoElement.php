<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLVideoAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLVideoElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLVideoAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLVideoAttributes $attributes = new HTMLVideoAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"video",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
