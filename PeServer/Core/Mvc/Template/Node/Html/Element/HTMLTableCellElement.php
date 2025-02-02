<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableDataCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

/**  */
class HTMLTableCellElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param "td"|"th" $tagName
	 * @param HTMLTableDataCellAttributes|HTMLTableHeaderCellAttributes|null $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		string $tagName,
		HTMLTableDataCellAttributes|HTMLTableHeaderCellAttributes|null $attributes = null,
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			$tagName,
			$attributes ?? match ($tagName) {
				"td" => new HTMLTableDataCellAttributes(),
				"th" => new HTMLTableHeaderCellAttributes(),
			},
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
