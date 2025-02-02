<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLProgressAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLProgressElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLProgressAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLProgressAttributes $attributes = new HTMLProgressAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"progress",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}
}
