<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBaseAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;
use PeServer\Core\Mvc\Template\Node\TextNode;
use stdClass;

/**
 * 簡易生成処理。
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
	 * `<title>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param INode $child
	 * @param object $props
	 * @return HTMLTitleElement
	 */
	public function title(HtmlAttributes $attributes = new HtmlAttributes(), INode $child = new TextNode(""), object $props = new stdClass()): HTMLTitleElement
	{
		return new HTMLTitleElement(
			$attributes,
			$child,
			$props
		);
	}

	/**
	 * `<base>`
	 *
	 * @param HTMLBaseAttributes $attributes
	 * @param object $props
	 * @return HTMLBaseElement
	 */
	public function base(HTMLBaseAttributes $attributes = new HTMLBaseAttributes(), object $props = new stdClass()): HTMLBaseElement
	{
		return new HTMLBaseElement(
			$attributes,
			$props
		);
	}

	/**
	 * `<link>`
	 *
	 * @param HTMLLinkAttributes $attributes
	 * @param object $props
	 * @return HTMLLinkElement
	 */
	public function link(HTMLLinkAttributes $attributes = new HTMLLinkAttributes(), object $props = new stdClass()): HTMLLinkElement
	{
		return new HTMLLinkElement(
			$attributes,
			$props
		);
	}

	public function style(HTMLStyleAttributes $attributes = new HTMLStyleAttributes(), INode $child = new TextNode(''), object $props = new stdClass()): HTMLStyleElement
	{
		return new HTMLStyleElement(
			$attributes,
			$child,
			$props
		);
	}

	public function script(HTMLScriptAttributes $attributes = new HTMLScriptAttributes(), INode $child = new TextNode(''), object $props = new stdClass()): HTMLScriptElement
	{
		return new HTMLScriptElement(
			$attributes,
			$child,
			$props
		);
	}

	public function meta(HTMLMetaAttributes $attributes = new HTMLMetaAttributes(), object $props = new stdClass()): HTMLMetaElement
	{
		return new HTMLMetaElement(
			$attributes,
			$props
		);
	}

	/**
	 * `<noscript>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 * @return HTMLElement
	 */
	public function noscript(HTMLAttributes $attributes = new HTMLAttributes(), array $children = [], object $props = new stdClass()): HTMLElement
	{
		return new HTMLElement(
			"noscript",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<template>`
	 *
	 * @param HTMLTemplateAttributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 * @return HTMLTemplateElement
	 */
	public function template(HTMLTemplateAttributes $attributes = new HTMLTemplateAttributes(), array $children = [], object $props = new stdClass()): HTMLTemplateElement
	{
		return new HTMLTemplateElement(
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
