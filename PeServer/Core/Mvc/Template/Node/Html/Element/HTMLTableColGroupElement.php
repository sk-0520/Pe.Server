<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableColGroupElement extends HTMLElement implements IHTMLTableColElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTableColAttributes $attributes
	 * @param HtmlContent $content,

	 */
	public function __construct(
		HTMLTableColAttributes $attributes = new HTMLTableColAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"colgroup",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
