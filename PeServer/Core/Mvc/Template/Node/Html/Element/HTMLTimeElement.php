<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTimeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTimeElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTimeAttributes $attributes
	 * @param HtmlContent $content,
	 * @param Props $props
	 */
	public function __construct(
		HTMLTimeAttributes $attributes = new HTMLTimeAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"time",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::inline(false)
		);
	}
}
