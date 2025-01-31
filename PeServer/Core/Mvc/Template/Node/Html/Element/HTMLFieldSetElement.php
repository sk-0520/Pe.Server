<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFieldSetAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLFieldSetElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLFieldSetAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLFieldSetAttributes $attributes = new HTMLFieldSetAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"fieldset",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
