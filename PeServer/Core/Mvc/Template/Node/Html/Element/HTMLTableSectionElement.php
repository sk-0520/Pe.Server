<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLTableSectionElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param "tbody"|"thead"|"tfoot" $tagName
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content,

	 */
	public function __construct(
		string $tagName,
		HtmlAttributes $attributes = new HtmlAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			$tagName,
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
