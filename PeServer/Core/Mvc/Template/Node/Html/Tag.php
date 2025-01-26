<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;

class Tag
{
	#region function

	/**
	 *
	 * @param HtmlAttributes $attributes
	 * @param INode[] $children
	 * @param array<mixed> $children
	 * @param array<string,mixed> $props
	 * @return HTMLElement
	 */
	public function html(HtmlAttributes $attributes = new HtmlAttributes([]), array $children = [], array $props = []): HTMLElement
	{
		return new HTMLElement(
			"html",
			$attributes,
			$children,
			$props
		);
	}

	#endregion
}
