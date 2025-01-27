<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;
use stdClass;

/**
 * 簡易生成処理。
 *
 * 具象クラスじゃなくてこっちで型判定する方針。
 */
class Tag
{
	#region function

	/**
	 * <html>
	 *
	 * @param HTMLHtmlAttributes $attributes
	 * @param array<HTMLHeadElement|HTMLBodyElement> $children
	 * @param object $props
	 * @return HTMLHtmlElement
	 */
	public function html(HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(), array $children = [], object $props = new stdClass()): HTMLHtmlElement
	{
		return new HTMLHtmlElement(
			$attributes,
			$children,
			$props
		);
	}

	/**
	 * `<head>`
	 *
	 * @param HTMLHeadAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 * @return HTMLHeadElement
	 */
	public function head(HTMLHeadAttributes $attributes = new HTMLHeadAttributes(), array $children = [], object $props = new stdClass()): HTMLHeadElement
	{
		return new HTMLHeadElement(
			$attributes,
			$children,
			$props
		);
	}


	/**
	 * `<body>`
	 *
	 * @param HTMLBodyAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 * @return HTMLBodyElement
	 */
	public function body(HTMLBodyAttributes $attributes = new HTMLBodyAttributes(), array $children = [], object $props = new stdClass()): HTMLBodyElement
	{
		return new HTMLBodyElement(
			$attributes,
			$children,
			$props
		);
	}

	#endregion
}
