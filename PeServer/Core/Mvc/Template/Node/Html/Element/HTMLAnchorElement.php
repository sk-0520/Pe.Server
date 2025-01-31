<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLAnchorElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAnchorAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLAnchorAttributes $attributes = new HTMLAnchorAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"a",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
