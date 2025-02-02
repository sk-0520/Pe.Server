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

	 */
	public function __construct(
		HTMLTableColAttributes $attributes = new HTMLTableColAttributes(),
	) {
		parent::__construct(
			"col",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::inline(true)
		);
	}
}
