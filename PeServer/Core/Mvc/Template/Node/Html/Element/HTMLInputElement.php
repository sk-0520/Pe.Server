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

	 */
	public function __construct(
		HTMLInputAttributes $attributes = new HTMLInputAttributes(),
	) {
		parent::__construct(
			"input",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::inline(true)
		);
	}
}
