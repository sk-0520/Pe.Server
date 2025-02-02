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
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLCanvasAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDetailsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDialogAttribute;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLEmbedAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFieldSetAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHeadAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLIFrameAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImageAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLInputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLInsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLIAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMeterAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLModAttribute;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLObjectAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOListAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptGroupAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptionAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOutputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLProgressAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLQuoteAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSelectAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSlotAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSourceAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableDataCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeadCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableRowAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTextAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTimeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTrackAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLUListAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLVideoAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLAnchorElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLAreaElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLAudioElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLBaseElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLBodyElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLBrElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLButtonElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLCanvasElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLDataElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLDataListElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLDetailsElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLDialogElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLDivElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLDListElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HtmlElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLEmbedElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLFieldSetElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLFormElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLHeadElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLHeadingElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLHRElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLHtmlElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLIFrameElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLImageElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLInputElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLLabelElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLLegendElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLLIElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLLinkElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLMapElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLMenuElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLMetaElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLMeterElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLObjectElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLOListElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLOptGroupElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLOptionElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLOutputElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLParagraphElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLPictureElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLPreElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLProgressElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLQuoteElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLScriptElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLSelectElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLSlotElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLSourceElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLSpanElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLStyleElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableCaptionElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableCellElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableColElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableColGroupElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableRowElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableSectionElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTemplateElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTextAreaElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTimeElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTitleElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTrackElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLUListElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLVideoElement;
use PeServer\Core\Mvc\Template\Node\Html\Element\IHTMLModElement;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextContent;
use PeServer\Core\Mvc\Template\Node\TextNode;
use Smarty\FunctionHandler\HtmlOptions;

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
	 * @param HtmlContent $content
	 * @return HTMLHtmlElement
	 */
	public function html(HTMLHtmlAttributes $attributes = new HTMLHtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHtmlElement
	{
		return new HTMLHtmlElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<head>`
	 *
	 * @param HTMLHeadAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLHeadElement
	 */
	public function head(HTMLHeadAttributes $attributes = new HTMLHeadAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadElement
	{
		return new HTMLHeadElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<title>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param TextContent $content
	 * @return HTMLTitleElement
	 */
	public function title(HtmlAttributes $attributes = new HtmlAttributes(), TextContent $content = new TextContent("")): HTMLTitleElement
	{
		return new HTMLTitleElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<base>`
	 *
	 * @param HTMLBaseAttributes $attributes
	 * @return HTMLBaseElement
	 */
	public function base(HTMLBaseAttributes $attributes = new HTMLBaseAttributes()): HTMLBaseElement
	{
		return new HTMLBaseElement(
			$attributes,
		);
	}

	/**
	 * `<link>`
	 *
	 * @param HTMLLinkAttributes $attributes

	 * @return HTMLLinkElement
	 */
	public function link(HTMLLinkAttributes $attributes = new HTMLLinkAttributes()): HTMLLinkElement
	{
		return new HTMLLinkElement(
			$attributes,
		);
	}

	public function style(HTMLStyleAttributes $attributes = new HTMLStyleAttributes(), TextContent $content = new TextContent()): HTMLStyleElement
	{
		return new HTMLStyleElement(
			$attributes,
			$content
		);
	}

	public function script(HTMLScriptAttributes $attributes = new HTMLScriptAttributes(), TextContent $content = new TextContent()): HTMLScriptElement
	{
		return new HTMLScriptElement(
			$attributes,
			$content
		);
	}

	public function meta(HTMLMetaAttributes $attributes = new HTMLMetaAttributes()): HTMLMetaElement
	{
		return new HTMLMetaElement(
			$attributes,
		);
	}

	/**
	 * `<noscript>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function noscript(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"noscript",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<template>`
	 *
	 * @param HTMLTemplateAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLTemplateElement
	 */
	public function template(HTMLTemplateAttributes $attributes = new HTMLTemplateAttributes(), HtmlContent $content = new HtmlContent()): HTMLTemplateElement
	{
		return new HTMLTemplateElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<body>`
	 *
	 * @param HTMLBodyAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLBodyElement
	 */
	public function body(HTMLBodyAttributes $attributes = new HTMLBodyAttributes(), HtmlContent $content = new HtmlContent()): HTMLBodyElement
	{
		return new HTMLBodyElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<a>`
	 *
	 * @param HTMLAnchorAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLAnchorElement
	 */
	public function a(HTMLAnchorAttributes $attributes = new HTMLAnchorAttributes(), HtmlContent $content = new HtmlContent()): HTMLAnchorElement
	{
		return new HTMLAnchorElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<abbr>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function abbr(HTMLAttributes $attributes = new HTMLAnchorAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"abbr",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<abbr>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function address(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"address",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<area>`
	 *
	 * @param HTMLAreaAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLAreaElement
	 */
	public function area(HTMLAreaAttributes $attributes = new HTMLAreaAttributes(), HtmlContent $content = new HtmlContent()): HTMLAreaElement
	{
		return new HTMLAreaElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<article>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function article(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"article",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<aside>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function aside(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"aside",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<area>`
	 *
	 * @param HTMLAudioAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLAudioElement
	 */
	public function audio(HTMLAudioAttributes $attributes = new HTMLAudioAttributes(), HtmlContent $content = new HtmlContent()): HTMLAudioElement
	{
		return new HTMLAudioElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<b>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function b(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"b",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<bdi>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function bdi(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"bdi",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<bdo>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function bdo(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"bdo",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<blockquote>`
	 *
	 * @param HTMLQuoteAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLQuoteElement
	 */
	public function blockquote(HTMLQuoteAttributes $attributes = new HTMLQuoteAttributes(), HtmlContent $content = new HtmlContent()): HTMLQuoteElement
	{
		return new HTMLQuoteElement(
			false,
			$attributes,
			$content
		);
	}

	/**
	 * `<blockquote>`
	 *
	 * @param HTMLAttributes $attributes
	 * @return HTMLBrElement
	 */
	public function br(HTMLAttributes $attributes = new HTMLAttributes()): HTMLBrElement
	{
		return new HTMLBrElement(
			$attributes,
		);
	}

	/**
	 * `<button>`
	 *
	 * @param HTMLButtonAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLButtonElement
	 */
	public function button(HTMLButtonAttributes $attributes = new HTMLButtonAttributes(), HtmlContent $content = new HtmlContent()): HTMLButtonElement
	{
		return new HTMLButtonElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<canvas>`
	 *
	 * @param HTMLCanvasAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLCanvasElement
	 */
	public function canvas(HTMLCanvasAttributes $attributes = new HTMLCanvasAttributes(), HtmlContent $content = new HtmlContent()): HTMLCanvasElement
	{
		return new HTMLCanvasElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<caption>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLTableCaptionElement
	 */
	public function caption(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableCaptionElement
	{
		return new HTMLTableCaptionElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<bdo>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function cite(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"cite",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<code>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function code(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"code",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<code>`
	 *
	 * @param HTMLTableColAttributes $attributes
	 * @return HTMLTableColElement
	 */
	public function col(HTMLTableColAttributes $attributes = new HTMLTableColAttributes()): HTMLTableColElement
	{
		return new HTMLTableColElement(
			$attributes,
		);
	}

	/**
	 * `<colgroup>`
	 *
	 * @param HTMLTableColAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLTableColGroupElement
	 */
	public function colgroup(HTMLTableColAttributes $attributes = new HTMLTableColAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableColGroupElement
	{
		return new HTMLTableColGroupElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<data>`
	 *
	 * @param HTMLDataAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLDataElement
	 */
	public function data(HTMLDataAttributes $attributes = new HTMLDataAttributes(), HtmlContent $content = new HtmlContent()): HTMLDataElement
	{
		return new HTMLDataElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<data>`
	 *
	 * @param HTMLDataAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLDataListElement
	 */
	public function datalist(HTMLDataAttributes $attributes = new HTMLDataAttributes(), HtmlContent $content = new HtmlContent()): HTMLDataListElement
	{
		return new HTMLDataListElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<dd>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function dd(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"dd",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<dd>`
	 *
	 * @param HTMLModAttribute $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement&IHTMLModElement
	 */
	public function del(HTMLModAttribute $attributes = new HTMLModAttribute(), HtmlContent $content = new HtmlContent()): HTMLElement&IHTMLModElement
	{
		//phpcs:ignore PSR12.Classes.AnonClassDeclaration.SpaceAfterKeyword
		return new class(
			"del",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		) extends HTMLElement implements IHTMLModElement
		{
			//NOP
		};
	}

	/**
	 * `<dd>`
	 *
	 * @param HTMLDetailsAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLDetailsElement
	 */
	public function details(HTMLDetailsAttributes $attributes = new HTMLDetailsAttributes(), HtmlContent $content = new HtmlContent()): HTMLDetailsElement
	{
		return new HTMLDetailsElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<dd>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function dfn(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"dfn",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<dd>`
	 *
	 * @param HTMLDialogAttribute $attributes
	 * @param HtmlContent $content
	 * @return HTMLDialogElement
	 */
	public function dialog(HTMLDialogAttribute $attributes = new HTMLDialogAttribute(), HtmlContent $content = new HtmlContent()): HTMLDialogElement
	{
		return new HTMLDialogElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<div>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLDivElement
	 */
	public function div(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLDivElement
	{
		return new HTMLDivElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<div>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLDListElement
	 */
	public function dl(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLDListElement
	{
		return new HTMLDListElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<dt>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function dt(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"dt",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<em>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function em(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"em",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<embed>`
	 *
	 * @param HTMLEmbedAttributes $attributes
	 * @return HTMLEmbedElement
	 */
	public function embed(HTMLEmbedAttributes $attributes = new HTMLEmbedAttributes()): HTMLEmbedElement
	{
		return new HTMLEmbedElement(
			$attributes,
		);
	}

	/**
	 * `<fieldset>`
	 *
	 * @param HTMLFieldSetAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLFieldSetElement
	 */
	public function fieldset(HTMLFieldSetAttributes $attributes = new HTMLFieldSetAttributes(), HtmlContent $content = new HtmlContent()): HTMLFieldSetElement
	{
		return new HTMLFieldSetElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<figcaption>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLElement
	 */
	public function figcaption(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"figcaption",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<figure>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function figure(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"figure",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<footer>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function footer(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"footer",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<form>`
	 *
	 * @param HTMLFormAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLFormElement
	 */
	public function form(HTMLFormAttributes $attributes = new HTMLFormAttributes(), HtmlContent $content = new HtmlContent()): HTMLFormElement
	{
		return new HTMLFormElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<h*>`
	 *
	 * @param 1|2|3|4|5|6 $level
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLHeadingElement
	 */
	public function h(int $level, HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return new HTMLHeadingElement(
			$level,
			$attributes,
			$content
		);
	}

	/**
	 * `<h1>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content
	 * @return HTMLHeadingElement
	 */
	public function h1(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return $this->h(1, $attributes, $content);
	}

	/**
	 * `<h2>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLHeadingElement
	 */
	public function h2(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return $this->h(2, $attributes, $content);
	}

	/**
	 * `<h3>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLHeadingElement
	 */
	public function h3(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return $this->h(3, $attributes, $content);
	}

	/**
	 * `<h4>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLHeadingElement
	 */
	public function h4(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return $this->h(4, $attributes, $content);
	}

	/**
	 * `<h5>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLHeadingElement
	 */
	public function h5(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return $this->h(5, $attributes, $content);
	}

	/**
	 * `<h6>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLHeadingElement
	 */
	public function h6(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLHeadingElement
	{
		return $this->h(6, $attributes, $content);
	}

	/**
	 * `<header>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function header(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"header",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<header>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function hgroup(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"hgroup",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<hr>`
	 *
	 * @param HTMLAttributes $attributes

	 * @return HTMLHRElement
	 */
	public function hr(HTMLAttributes $attributes = new HTMLAttributes()): HTMLHRElement
	{
		return new HTMLHRElement(
			$attributes,
		);
	}

	/**
	 * `<i>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function i(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"i",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<iframe>`
	 *
	 * @param HTMLIFrameAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLIFrameElement
	 */
	public function iframe(HTMLIFrameAttributes $attributes = new HTMLIFrameAttributes(), HtmlContent $content = new HtmlContent()): HTMLIFrameElement
	{
		return new HTMLIFrameElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<img>`
	 *
	 * @param HTMLImageAttributes $attributes

	 * @return HTMLImageElement
	 */
	public function img(HTMLImageAttributes $attributes = new HTMLImageAttributes()): HTMLImageElement
	{
		return new HTMLImageElement(
			$attributes,
		);
	}

	/**
	 * `<input>`
	 *
	 * @param HTMLInputAttributes $attributes

	 * @return HTMLInputElement
	 */
	public function input(HTMLInputAttributes $attributes = new HTMLInputAttributes()): HTMLInputElement
	{
		return new HTMLInputElement(
			$attributes,
		);
	}

	/**
	 * `<i>`
	 *
	 * @param HTMLInsAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement&IHTMLModElement
	 */
	public function ins(HTMLInsAttributes $attributes = new HTMLInsAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement&IHTMLModElement
	{
		//phpcs:ignore PSR12.Classes.AnonClassDeclaration.SpaceAfterKeyword
		return new class(
			"ins",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		) extends HTMLElement implements IHTMLModElement
		{
			//NOP
		};
	}

	/**
	 * `<i>`
	 *
	 * @param HTMLInsAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function kbd(HTMLInsAttributes $attributes = new HTMLInsAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"kbd",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<label>`
	 *
	 * @param HTMLLabelAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLLabelElement
	 */
	public function label(HTMLLabelAttributes $attributes = new HTMLLabelAttributes(), HtmlContent $content = new HtmlContent()): HTMLLabelElement
	{
		return new HTMLLabelElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<legend>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLLegendElement
	 */
	public function legend(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLLegendElement
	{
		return new HTMLLegendElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<li>`
	 *
	 * @param HTMLLIAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLLIElement
	 */
	public function li(HTMLLIAttributes $attributes = new HTMLLIAttributes(), HtmlContent $content = new HtmlContent()): HTMLLIElement
	{
		return new HTMLLIElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<main>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function main(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"main",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<main>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLMapElement
	 */
	public function map(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLMapElement
	{
		return new HTMLMapElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<mark>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function mark(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"mark",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<menu>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLMenuElement
	 */
	public function menu(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLMenuElement
	{
		return new HTMLMenuElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<menu>`
	 *
	 * @param HTMLMeterAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLMeterElement
	 */
	public function meter(HTMLMeterAttributes $attributes = new HTMLMeterAttributes(), HtmlContent $content = new HtmlContent()): HTMLMeterElement
	{
		return new HTMLMeterElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<nav>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function nav(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"nav",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<object>`
	 *
	 * @param HTMLObjectAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLObjectElement
	 */
	public function object(HTMLObjectAttributes $attributes = new HTMLObjectAttributes(), HtmlContent $content = new HtmlContent()): HTMLObjectElement
	{
		return new HTMLObjectElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<ol>`
	 *
	 * @param HTMLOListAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLOListElement
	 */
	public function ol(HTMLOListAttributes $attributes = new HTMLOListAttributes(), HtmlContent $content = new HtmlContent()): HTMLOListElement
	{
		return new HTMLOListElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<optgroup>`
	 *
	 * @param HTMLOptGroupAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLOptGroupElement
	 */
	public function optgroup(HTMLOptGroupAttributes $attributes = new HTMLOptGroupAttributes(), HtmlContent $content = new HtmlContent()): HTMLOptGroupElement
	{
		return new HTMLOptGroupElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<option>`
	 *
	 * @param HTMLOptionAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLOptionElement
	 */
	public function option(HTMLOptionAttributes $attributes = new HTMLOptionAttributes(), HtmlContent $content = new HtmlContent()): HTMLOptionElement
	{
		return new HTMLOptionElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<output>`
	 *
	 * @param HTMLOutputAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLOutputElement
	 */
	public function output(HTMLOutputAttributes $attributes = new HTMLOutputAttributes(), HtmlContent $content = new HtmlContent()): HTMLOutputElement
	{
		return new HTMLOutputElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<output>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLParagraphElement
	 */
	public function p(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLParagraphElement
	{
		return new HTMLParagraphElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<picture>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLPictureElement
	 */
	public function picture(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLPictureElement
	{
		return new HTMLPictureElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<pre>`
	 *
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLPreElement
	 */
	public function pre(HtmlAttributes $attributes = new HtmlAttributes(), HtmlContent $content = new HtmlContent()): HTMLPreElement
	{
		return new HTMLPreElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<progress>`
	 *
	 * @param HTMLProgressAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLProgressElement
	 */
	public function progress(HTMLProgressAttributes $attributes = new HTMLProgressAttributes(), HtmlContent $content = new HtmlContent()): HTMLProgressElement
	{
		return new HTMLProgressElement(
			$attributes,
			$content
		);
	}


	/**
	 * `<blockquote>`
	 *
	 * @param HTMLQuoteAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLQuoteElement
	 */
	public function q(HTMLQuoteAttributes $attributes = new HTMLQuoteAttributes(), HtmlContent $content = new HtmlContent()): HTMLQuoteElement
	{
		return new HTMLQuoteElement(
			true,
			$attributes,
			$content
		);
	}

	/**
	 * `<rp>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function rp(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"rp",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<rt>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function rt(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"rt",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<ruby>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function ruby(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"ruby",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<s>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function s(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"s",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<s>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function samp(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"samp",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<search>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function search(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"search",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<section>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function section(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"section",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<select>`
	 *
	 * @param HTMLSelectAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLSelectElement
	 */
	public function select(HTMLSelectAttributes $attributes = new HTMLSelectAttributes(), HtmlContent $content = new HtmlContent()): HTMLSelectElement
	{
		return new HTMLSelectElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<slot>`
	 *
	 * @param HTMLSlotAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLSlotElement
	 */
	public function slot(HTMLSlotAttributes $attributes = new HTMLSlotAttributes(), HtmlContent $content = new HtmlContent()): HTMLSlotElement
	{
		return new HTMLSlotElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<small>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function small(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"small",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<source>`
	 *
	 * @param HTMLSourceAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLSourceElement
	 */
	public function source(HTMLSourceAttributes $attributes = new HTMLSourceAttributes(), HtmlContent $content = new HtmlContent()): HTMLSourceElement
	{
		return new HTMLSourceElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<span>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLSpanElement
	 */
	public function span(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLSpanElement
	{
		return new HTMLSpanElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<strong>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function strong(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"strong",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<sub>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function sub(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"sub",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<summary>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function summary(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"summary",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}

	/**
	 * `<sup>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function sup(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"sup",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<table>`
	 *
	 * @param HTMLTableAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableElement
	 */
	public function table(HTMLTableAttributes $attributes = new HTMLTableAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableElement
	{
		return new HTMLTableElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<tbody>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableSectionElement
	 */
	public function tbody(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableSectionElement
	{
		return new HTMLTableSectionElement(
			"tbody",
			$attributes,
			$content
		);
	}

	/**
	 * `<td>`
	 *
	 * @param HTMLTableDataCellAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableCellElement
	 */
	public function td(HTMLTableDataCellAttributes $attributes = new HTMLTableDataCellAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableCellElement
	{
		return new HTMLTableCellElement(
			"td",
			$attributes,
			$content
		);
	}

	/**
	 * `<textarea>`
	 *
	 * @param HTMLTextAreaAttributes $attributes
	 * @param TextContent $content

	 * @return HTMLTextAreaElement
	 */
	public function textarea(HTMLTextAreaAttributes $attributes = new HTMLTextAreaAttributes(), TextContent $content = new TextContent("")): HTMLTextAreaElement
	{
		return new HTMLTextAreaElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<tfoot>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableSectionElement
	 */
	public function tfoot(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableSectionElement
	{
		return new HTMLTableSectionElement(
			"tfoot",
			$attributes,
			$content
		);
	}

	/**
	 * `<th>`
	 *
	 * @param HTMLTableHeaderCellAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableCellElement
	 */
	public function th(HTMLTableHeaderCellAttributes $attributes = new HTMLTableHeaderCellAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableCellElement
	{
		return new HTMLTableCellElement(
			"th",
			$attributes,
			$content
		);
	}

	/**
	 * `<thead>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableSectionElement
	 */
	public function thead(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableSectionElement
	{
		return new HTMLTableSectionElement(
			"thead",
			$attributes,
			$content
		);
	}

	/**
	 * `<time>`
	 *
	 * @param HTMLTimeAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTimeElement
	 */
	public function time(HTMLTimeAttributes $attributes = new HTMLTimeAttributes(), HtmlContent $content = new HtmlContent()): HTMLTimeElement
	{
		return new HTMLTimeElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<tr>`
	 *
	 * @param HTMLTableRowAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLTableRowElement
	 */
	public function tr(HTMLTableRowAttributes $attributes = new HTMLTableRowAttributes(), HtmlContent $content = new HtmlContent()): HTMLTableRowElement
	{
		return new HTMLTableRowElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<tr>`
	 *
	 * @param HTMLTrackAttributes $attributes

	 * @return HTMLTrackElement
	 */
	public function track(HTMLTrackAttributes $attributes = new HTMLTrackAttributes()): HTMLTrackElement
	{
		return new HTMLTrackElement(
			$attributes,
		);
	}

	/**
	 * `<u>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function u(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"u",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<ul>`
	 *
	 * @param HTMLUListAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLUListElement
	 */
	public function ul(HTMLUListAttributes $attributes = new HTMLUListAttributes(), HtmlContent $content = new HtmlContent()): HTMLUListElement
	{
		return new HTMLUListElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<var>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function var(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"var",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	/**
	 * `<video>`
	 *
	 * @param HTMLVideoAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLVideoElement
	 */
	public function video(HTMLVideoAttributes $attributes = new HTMLVideoAttributes(), HtmlContent $content = new HtmlContent()): HTMLVideoElement
	{
		return new HTMLVideoElement(
			$attributes,
			$content
		);
	}

	/**
	 * `<wbr>`
	 *
	 * @param HTMLAttributes $attributes
	 * @param HtmlContent $content

	 * @return HTMLElement
	 */
	public function wbr(HTMLAttributes $attributes = new HTMLAttributes(), HtmlContent $content = new HtmlContent()): HTMLElement
	{
		return new HTMLElement(
			"wbr",
			$attributes,
			$content,
			HtmlElementOptions::inline(false)
		);
	}

	#endregion
}
