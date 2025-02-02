<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMeterAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLMeterElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLMeterAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLMeterAttributes $attributes = new HTMLMeterAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"meter",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
