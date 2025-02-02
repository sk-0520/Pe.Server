<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Throws\ArgumentException;

class HTMLHeadingElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param 1|2|3|4|5|6 $level
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		int $level,
		HtmlAttributes $attributes = new HtmlAttributes(),
		HtmlContent $content = new HtmlContent(),
	) {
		//@phpstan-ignore smaller.alwaysFalse, smaller.alwaysFalse, booleanOr.alwaysFalse
		if ($level < 1 || 6 < $level) {
			throw new ArgumentException('1 < $level < 6');
		}

		parent::__construct(
			"h" . (string)$level,
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
