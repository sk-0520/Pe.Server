<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLHeadElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLHeadAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLHeadAttributes $attributes = new HTMLHeadAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"head",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
