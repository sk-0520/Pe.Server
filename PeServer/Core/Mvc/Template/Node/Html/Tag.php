<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;

class Tag
{
	#region function

	/**
	 * HTML
	 *
	 * @param HTMLHtmlAttributes $attributes
	 * @param INode[] $children
	 * @param array<mixed> $children
	 * @param array<string,mixed> $props
	 * @return HTMLHtmlElement
	 */
	public function html(HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(), array $children = [], array $props = []): HTMLHtmlElement
	{
		return new HTMLHtmlElement(
			$attributes,
			$children,
			$props
		);
	}

	#endregion
}
