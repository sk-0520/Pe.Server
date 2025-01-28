<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

class HTMLTemplateElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTemplateAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 */
	public function __construct(
		HTMLTemplateAttributes $attributes = new HTMLTemplateAttributes(),
		array $children = [],
		object $props = new stdClass()
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
