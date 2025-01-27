<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use stdClass;

class HTMLMetaElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLMetaAttributes $attributes
	 * @param object $props
	 */
	public function __construct(
		HTMLMetaAttributes $attributes = new HTMLMetaAttributes(),
		object $props = new stdClass()
	) {
		parent::__construct("meta", $attributes, [], $props, HtmlElementOptions::inline(true));
	}
}
