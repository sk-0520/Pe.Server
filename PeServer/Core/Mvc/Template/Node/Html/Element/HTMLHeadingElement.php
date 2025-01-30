<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Throws\ArgumentException;

class HTMLHeadingElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param 1|2|3|4|5|6 $level
	 * @param HtmlAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		int $level,
		HtmlAttributes $attributes = new HtmlAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		//@phpstan-ignore smaller.alwaysFalse, smaller.alwaysFalse, booleanOr.alwaysFalse
		if ($level < 1 || 6 < $level) {
			throw new ArgumentException('1 < $level < 6');
		}

		parent::__construct(
			"h" . (string)$level,
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
