<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;

class HTMLTemplateElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTemplateAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLTemplateAttributes $attributes = new HTMLTemplateAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"template",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
