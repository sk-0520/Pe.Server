<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use stdClass;

class HTMLBodyElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLBodyAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLBodyAttributes $attributes = new HTMLBodyAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"body",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
