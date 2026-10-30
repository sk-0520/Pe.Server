<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAudioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBaseAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLCanvasAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDetailsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDialogAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLEmbedAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFieldsetAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLIframeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImgAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLInputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLInsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLObjectAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptgroupAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOptionAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOutputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLProgressAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLQuoteAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSelectAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSourceAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableColGroupAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTextAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTimeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTrackAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLVideoAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputCheckboxAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputColorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputDateAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputDateTimeLocalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputEmailAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputFileAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputImageAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputMonthAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputNumberAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputPasswordAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRadioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRangeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputResetAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputSearchAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputSubmitAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTextAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTimeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputUrlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlElement;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NodeBuilder;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Throws\ArgumentException;

class HtmlBuilder extends NodeBuilder
{
	//TODO: void 要素の子が `new NoneNode()` と `NoneNode::get()` で混在しているので統一する
	#region define

	public const HTML_VERSION_5 = "5.0";

	#endregion

	#region variable

	/** @var self::HTML_VERSION_* */
	public string $version = self::HTML_VERSION_5;

	#endregion

	#region function

	public function a(HTMLAnchorAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'a',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function abbr(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'abbr',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function address(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'address',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function area(HTMLAreaAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'area',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: false, selfClosing: true)
		);
	}

	public function article(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'article',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function aside(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'aside',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function audio(HTMLAudioAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'audio',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}


	public function b(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'b',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function base(HTMLBaseAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'base',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function bdi(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'bdi',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function bdo(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'bdo',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function blockquote(HTMLQuoteAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'blockquote',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function body(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'body',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function br(HTMLBrAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'br',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function button(HTMLButtonAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'button',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function canvas(HTMLCanvasAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'canvas',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function caption(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'caption',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function cite(HTMLQuoteAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'cite',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function code(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'code',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function col(HTMLTableColAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'col',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function colgroup(HTMLTableColGroupAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'colgroup',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function data(HTMLDataAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'data',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function datalist(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'datalist',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dd(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'dd',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function del(HTMLDelAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'del',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function details(HTMLDetailsAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'details',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dfn(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'dfn',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function dialog(HTMLDialogAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'dialog',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function div(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'div',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dl(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'dl',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dt(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'dt',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function em(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'em',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function embed(HTMLEmbedAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'embed',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function fieldset(HTMLFieldsetAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'fieldset',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function figcaption(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'figcaption',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function figure(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'figure',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function footer(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'footer',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function form(HTMLFormAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'form',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	/**
	 * h1-6
	 *
	 * @param int<1,6> $level
	 * @param HtmlGlobalAttributes $attributes
	 * @param INode $children
	 * @return HtmlElement
	 */
	public function heading(int $level, HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'h' . $level,
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	#region heading
	public function h1(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return $this->heading(1, $attributes, $children);
	}

	public function h2(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return $this->heading(2, $attributes, $children);
	}

	public function h3(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return $this->heading(3, $attributes, $children);
	}

	public function h4(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return $this->heading(4, $attributes, $children);
	}

	public function h5(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return $this->heading(5, $attributes, $children);
	}

	public function h6(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return $this->heading(6, $attributes, $children);
	}
	#endregion

	public function head(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'head',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function header(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'header',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function hgroup(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'hgroup',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function hr(HtmlGlobalAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'hr',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: false, selfClosing: true)
		);
	}

	public function html(HTMLHtmlAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'html',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function i(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'i',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function iframe(HTMLIframeAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'iframe',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function img(HTMLImgAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'img',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function input(HTMLInputAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'input',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}
	#region input
	public function inputButton(HTMLInputButtonAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputCheckbox(HTMLInputCheckboxAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputColor(HTMLInputColorAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputDate(HTMLInputDateAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputDateTimeLocal(HTMLInputDateTimeLocalAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputEmail(HTMLInputEmailAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputFile(HTMLInputFileAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputHidden(HTMLInputHiddenAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputImage(HTMLInputImageAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputMonth(HTMLInputMonthAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputNumber(HTMLInputNumberAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputPassword(HTMLInputPasswordAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputRadio(HTMLInputRadioAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputRange(HTMLInputRangeAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputReset(HTMLInputResetAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputSearch(HTMLInputSearchAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputSubmit(HTMLInputSubmitAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputTel(HTMLInputTelAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputText(HTMLInputTextAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputTime(HTMLInputTimeAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	public function inputUrl(HTMLInputUrlAttributes $attributes): HtmlElement
	{
		return $this->input($attributes);
	}
	// week はよくわかんねぇなぁって感じで未着手(使わないし)
	// public function inputWeek(HTMLInputWeekAttributes $attributes): HtmlElement
	// {
	// 	return $this->input($attributes);
	// }
	#endregion

	public function ins(HTMLInsAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'ins',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}


	public function kbd(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'kbd',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function label(HTMLLabelAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'label',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function legend(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'legend',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function li(HTMLLiAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'li',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function link(HTMLLinkAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'link',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function main(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'main',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function map(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'map',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function mark(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'mark',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function menu(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'menu',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function meta(HTMLMetaAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'meta',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function meter(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'meter',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function nav(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'nav',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function noscript(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'noscript',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function object(HTMLObjectAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'object',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function ol(HTMLOlAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'ol',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function optgroup(HTMLOptgroupAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'optgroup',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function option(HTMLOptionAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'option',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function output(HTMLOutputAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'output',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function p(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'p',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function picture(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'picture',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function pre(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'pre',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function progress(HTMLProgressAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'progress',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function q(HTMLQuoteAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'q',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function rp(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'rp',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function rt(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'rt',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function ruby(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'ruby',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function s(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			's',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function samp(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'samp',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function script(HTMLScriptAttributes $attributes, ScriptSourceNodeBase|null $children = null): HtmlElement
	{
		if ($children !== null && !$children->isAllowedType($attributes->type)) {
			throw new ArgumentException('type');
		}
		//TODO: src 指定時は本文を空にする(仕様上 src と本文は両立しない)

		return new HtmlElement(
			'script',
			$attributes,
			$children ?? NoneNode::get(),
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function search(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'search',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function section(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'section',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function select(HTMLSelectAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'select',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function slot(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'slot',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function small(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'small',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function source(HTMLSourceAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'source',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: false, selfClosing: true)
		);
	}

	public function span(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'span',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function strong(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'strong',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function style(HTMLStyleAttributes $attributes, CssSourceNode|null $children = null): HtmlElement
	{
		return new HtmlElement(
			'style',
			$attributes,
			$children ?? NoneNode::get(),
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function sub(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'sub',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function summary(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'summary',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function sup(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'sup',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function table(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'table',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function tbody(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'tbody',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function td(HTMLTableCellAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'td',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function template(HTMLTemplateAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'template',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function textarea(HTMLTextAreaAttributes $attributes, INode $children): HtmlElement
	{
		//TODO: RCDATA 要素なので子はテキストのみに制限する(ESCAPE_RAW も不可)
		return new HtmlElement(
			'textarea',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function tfoot(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'tfoot',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function th(HTMLTableHeaderCellAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'th',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function thead(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'thead',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function time(HTMLTimeAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'time',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function title(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		//TODO: RCDATA 要素なので子はテキストのみに制限する(ESCAPE_RAW も不可)
		return new HtmlElement(
			'title',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function tr(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'tr',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function track(HTMLTrackAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'track',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function u(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'u',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function ul(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'ul',
			$attributes,
			$children,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function var(HtmlGlobalAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'var',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function video(HTMLVideoAttributes $attributes, INode $children): HtmlElement
	{
		return new HtmlElement(
			'video',
			$attributes,
			$children,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function wbr(HtmlGlobalAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'wbr',
			$attributes,
			NoneNode::get(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	#endregion

	#region NodeBuilder

	#[Override]
	public function build(INode $node, ?BuildOptions $buildOptions = null): string
	{
		$docType = '<!DOCTYPE html>' . PHP_EOL;
		return $docType . parent::build($node, $buildOptions);
	}

	#endregion
}
