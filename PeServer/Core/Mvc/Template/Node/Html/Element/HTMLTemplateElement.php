<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;

class HTMLTemplateElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTemplateAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLTemplateAttributes $attributes = new HTMLTemplateAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"template",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
