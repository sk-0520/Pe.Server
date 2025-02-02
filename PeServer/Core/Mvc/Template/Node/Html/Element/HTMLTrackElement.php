<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTrackAttributes;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTrackElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLTrackAttributes $attributes

	 */
	public function __construct(
		HTMLTrackAttributes $attributes = new HTMLTrackAttributes(),
	) {
		parent::__construct(
			"track",
			$attributes,
			new NoneContent(),
			HtmlElementOptions::inline(true)
		);
	}
}
