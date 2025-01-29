<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColAttributes;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableColElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param "col"|"colgroup" $tagName,
	 * @param HTMLTableColAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		string $tagName,
		HTMLTableColAttributes $attributes = new HTMLTableColAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			$tagName,
			$attributes,
			[],
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
