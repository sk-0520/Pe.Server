<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLMetaElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLMetaAttributes $attributes
	 * @param Props $props
	 */
	public function __construct(
		HTMLMetaAttributes $attributes = new HTMLMetaAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"meta",
			$attributes,
			new NoneContent(),
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
