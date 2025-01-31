<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlNoneContent;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableColElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableColAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLTableColAttributes $attributes = new HTMLTableColAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"col",
			$attributes,
			new NoneContent(),
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
