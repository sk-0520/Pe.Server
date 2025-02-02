<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLUListAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLUListElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLUListAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLUListAttributes $attributes = new HTMLUListAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"ul",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
