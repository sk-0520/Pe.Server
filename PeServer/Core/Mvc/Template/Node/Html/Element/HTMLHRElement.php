<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLHRElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HtmlAttributes $attributes

	 */
	public function __construct(
		HtmlAttributes $attributes = new HtmlAttributes(),
	) {
		parent::__construct(
			"hr",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::inline(true)
		);
	}
}
