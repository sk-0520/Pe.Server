<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAudioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBaseAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLAnchorElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLAreaElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLAudioElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLBaseElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLBodyElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HtmlElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLHeadElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLHtmlElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLLinkElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLMetaElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLScriptElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLStyleElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTemplateElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTitleElement;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;


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
	 * @param Props $props
	 * @return HTMLHtmlElement
	 */
	public function html(HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(), array $children = [], Props $props = new Props()): HTMLHtmlElement
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
	 * @param Props $props
	 * @return HTMLHeadElement
	 */
	public function head(HTMLHeadAttributes $attributes = new HTMLHeadAttributes(), array $children = [], Props $props = new Props()): HTMLHeadElement
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
	 * @param Props $props
	 * @return HTMLTitleElement
	 */
	public function title(HtmlAttributes $attributes = new HtmlAttributes(), INode $child = new TextNode(""), Props $props = new Props()): HTMLTitleElement
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
	 * @param Props $props
	 * @return HTMLBaseElement
	 */
	public function base(HTMLBaseAttributes $attributes = new HTMLBaseAttributes(), Props $props = new Props()): HTMLBaseElement
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
	 * @param Props $props
	 * @return HTMLLinkElement
	 */
	public function link(HTMLLinkAttributes $attributes = new HTMLLinkAttributes(), Props $props = new Props()): HTMLLinkElement
	{
		return new HTMLLinkElement(
			$attributes,
			$props
		);
	}

	public function style(HTMLStyleAttributes $attributes = new HTMLStyleAttributes(), INode $child = new TextNode(''), Props $props = new Props()): HTMLStyleElement
	{
		return new HTMLStyleElement(
			$attributes,
			$child,
			$props
		);
	}

	public function script(HTMLScriptAttributes $attributes = new HTMLScriptAttributes(), INode $child = new TextNode(''), Props $props = new Props()): HTMLScriptElement
	{
		return new HTMLScriptElement(
			$attributes,
			$child,
			$props
		);
	}

	public function meta(HTMLMetaAttributes $attributes = new HTMLMetaAttributes(), Props $props = new Props()): HTMLMetaElement
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
	 * @param Props $props
	 * @return HTMLElement
	 */
	public function noscript(HTMLAttributes $attributes = new HTMLAttributes(), array $children = [], Props $props = new Props()): HTMLElement
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
	 * @param Props $props
	 * @return HTMLTemplateElement
	 */
	public function template(HTMLTemplateAttributes $attributes = new HTMLTemplateAttributes(), array $children = [], Props $props = new Props()): HTMLTemplateElement
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
	 * @param Props $props
	 * @return HTMLBodyElement
	 */
	public function body(HTMLBodyAttributes $attributes = new HTMLBodyAttributes(), array $children = [], Props $props = new Props()): HTMLBodyElement
	{
		return new HTMLBodyElement(
			$attributes,
			$children,
			$props
		);
	}

	/**
	 * `<a>`
	 *
	 * @param HTMLAnchorAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLAnchorElement
	 */
	public function anchor(HTMLAnchorAttributes $attributes = new HTMLAnchorAttributes(), array $children = [], Props $props = new Props()): HTMLAnchorElement
	{
		return new HTMLAnchorElement(
			$attributes,
			$children,
			$props
		);
	}

	/**
	 * `<abbr>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLElement
	 */
	public function abbr(HTMLAttributes $attributes = new HTMLAnchorAttributes(), array $children = [], Props $props = new Props()): HTMLElement
	{
		return new HTMLElement(
			"abbr",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<abbr>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLElement
	 */
	public function address(HTMLAttributes $attributes = new HTMLAttributes(), array $children = [], Props $props = new Props()): HTMLElement
	{
		return new HTMLElement(
			"address",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<area>`
	 *
	 * @param HTMLAreaAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLAreaElement
	 */
	public function area(HTMLAreaAttributes $attributes = new HTMLAreaAttributes(), array $children = [], Props $props = new Props()): HTMLAreaElement
	{
		return new HTMLAreaElement(
			$attributes,
			$children,
			$props
		);
	}

	/**
	 * `<article>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLElement
	 */
	public function article(HTMLAttributes $attributes = new HTMLAttributes(), array $children = [], Props $props = new Props()): HTMLElement
	{
		return new HTMLElement(
			"article",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<aside>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLElement
	 */
	public function aside(HTMLAttributes $attributes = new HTMLAttributes(), array $children = [], Props $props = new Props()): HTMLElement
	{
		return new HTMLElement(
			"aside",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<area>`
	 *
	 * @param HTMLAudioAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @return HTMLAudioElement
	 */
	public function audio(HTMLAudioAttributes $attributes = new HTMLAudioAttributes(), array $children = [], Props $props = new Props()): HTMLAudioElement
	{
		return new HTMLAudioElement(
			$attributes,
			$children,
			$props
		);
	}

	#endregion
}
