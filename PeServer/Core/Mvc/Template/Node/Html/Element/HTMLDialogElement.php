<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDialogAttribute;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDialogElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLDialogAttribute $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLDialogAttribute $attributes = new HTMLDialogAttribute(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"dialog",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
