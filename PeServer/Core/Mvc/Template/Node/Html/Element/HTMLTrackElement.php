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
	 * @param Props $props
	 */
	public function __construct(
		HTMLTrackAttributes $attributes = new HTMLTrackAttributes(),
		Props $props = new Props()
	) {
		parent::__construct(
			"track",
			$attributes,
			new NoneContent(),
			$props,
			HtmlElementOptions::inline(true)
		);
	}
}
