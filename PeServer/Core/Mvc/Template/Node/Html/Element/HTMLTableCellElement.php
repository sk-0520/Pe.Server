<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableDataCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

/**  */
class HTMLTableCellElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param "td"|"th" $tagName
	 * @param HTMLTableDataCellAttributes|HTMLTableHeaderCellAttributes|null $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		string $tagName,
		HTMLTableDataCellAttributes|HTMLTableHeaderCellAttributes|null $attributes = null,
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			$tagName,
			$attributes ?? match ($tagName) {
				"td" => new HTMLTableDataCellAttributes(),
				"th" => new HTMLTableHeaderCellAttributes(),
			},
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
