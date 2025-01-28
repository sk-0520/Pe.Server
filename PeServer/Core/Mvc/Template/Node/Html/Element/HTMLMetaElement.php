<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

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
			[],
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
