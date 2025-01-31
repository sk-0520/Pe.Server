<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLInputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlNoneContent;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLInputElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLInputAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLInputAttributes $attributes = new HTMLInputAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"input",
			$attributes,
			new NoneContent(),
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
