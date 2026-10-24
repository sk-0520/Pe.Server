<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Content;
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
use PeServer\Core\Mvc\Template\Node\NoneContent;

class HtmlBuilder extends NodeBuilder
{
	#region define

	public const HTML_VERSION_5 = "5.0";

	#endregion

	#region variable

	/** @var self::HTML_VERSION_* */
	public string $version = self::HTML_VERSION_5;

	#endregion

	#region function

	public function a(HTMLAnchorAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'a',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function abbr(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'abbr',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function address(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'address',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function area(HTMLAreaAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'area',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: false, selfClosing: true)
		);
	}

	public function article(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'article',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function aside(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'aside',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function audio(HTMLAudioAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'audio',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}


	public function b(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'b',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function base(HTMLBaseAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'base',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function bdi(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'bdi',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function bdo(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'bdo',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function blockquote(HTMLQuoteAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'blockquote',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function body(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'body',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function br(HTMLBrAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'br',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function button(HTMLButtonAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'button',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function canvas(HTMLCanvasAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'canvas',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function caption(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'caption',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function cite(HTMLQuoteAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'cite',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function code(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'code',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function col(HTMLTableColAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'col',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function colgroup(HTMLTableColGroupAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'colgroup',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function data(HTMLDataAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'data',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function datalist(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'datalist',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dd(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'dd',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function del(HTMLDelAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'del',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function details(HTMLDetailsAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'details',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dfn(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'dfn',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function dialog(HTMLDialogAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'dialog',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function div(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'div',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dl(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'dl',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function dt(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'dt',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function em(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'em',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function embed(HTMLEmbedAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'embed',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function fieldset(HTMLFieldsetAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'fieldset',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function figcaption(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'figcaption',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function figure(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'figure',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function footer(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'footer',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function form(HTMLFormAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'form',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	/**
	 * h1-6
	 *
	 * @param int<1,6> $level
	 * @param HtmlGlobalAttributes $attributes
	 * @param Content $content
	 * @return HtmlElement
	 */
	public function heading(int $level, HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'h' . $level,
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	#region heading
	public function h1(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return $this->heading(1, $attributes, $content);
	}

	public function h2(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return $this->heading(2, $attributes, $content);
	}

	public function h3(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return $this->heading(3, $attributes, $content);
	}

	public function h4(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return $this->heading(4, $attributes, $content);
	}

	public function h5(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return $this->heading(5, $attributes, $content);
	}

	public function h6(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return $this->heading(6, $attributes, $content);
	}
	#endregion

	public function head(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'head',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function header(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'header',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function hgroup(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'hgroup',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function hr(HtmlGlobalAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'hr',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: false, selfClosing: true)
		);
	}

	public function html(HTMLHtmlAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'html',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function i(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'i',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function iframe(HTMLIframeAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'iframe',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function img(HTMLImgAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'img',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function input(HTMLInputAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'input',
			$attributes,
			new NoneContent(),
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

	public function ins(HTMLInsAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'ins',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}


	public function kbd(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'kbd',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function label(HTMLLabelAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'label',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function legend(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'legend',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function li(HTMLLiAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'li',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function link(HTMLLinkAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'link',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function main(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'main',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function map(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'map',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function mark(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'mark',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function menu(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'menu',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function meta(HTMLMetaAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'meta',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function meter(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'meter',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function nav(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'nav',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function noscript(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'noscript',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function object(HTMLObjectAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'object',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function ol(HTMLOlAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'ol',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function optgroup(HTMLOptgroupAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'optgroup',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function option(HTMLOptionAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'option',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function output(HTMLOutputAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'output',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function p(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'p',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function picture(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'picture',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function pre(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'pre',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function progress(HTMLProgressAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'progress',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function q(HTMLQuoteAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'q',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function rp(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'rp',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function rt(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'rt',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function ruby(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'ruby',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function s(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			's',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function samp(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'samp',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function script(HTMLScriptAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'script',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false, rawText: true)
		);
	}

	public function search(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'search',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function section(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'section',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function select(HTMLSelectAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'select',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function slot(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'slot',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function small(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'small',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function source(HTMLSourceAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'source',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: false, selfClosing: true)
		);
	}

	public function span(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'span',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function strong(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'strong',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function style(HTMLStyleAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'style',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false, rawText: true)
		);
	}

	public function sub(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'sub',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function summary(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'summary',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function sup(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'sup',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function table(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'table',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function tbody(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'tbody',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function td(HTMLTableCellAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'td',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function template(HTMLTemplateAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'template',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function textarea(HTMLTextAreaAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'textarea',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function tfoot(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'tfoot',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function th(HTMLTableHeaderCellAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'th',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function thead(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'thead',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function time(HTMLTimeAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'time',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function title(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'title',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function tr(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'tr',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function track(HTMLTrackAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'track',
			$attributes,
			new NoneContent(),
			new ElementOptions(isInline: true, selfClosing: true)
		);
	}

	public function u(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'u',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function ul(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'ul',
			$attributes,
			$content,
			new ElementOptions(isInline: false, selfClosing: false)
		);
	}

	public function var(HtmlGlobalAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'var',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function video(HTMLVideoAttributes $attributes, Content $content): HtmlElement
	{
		return new HtmlElement(
			'video',
			$attributes,
			$content,
			new ElementOptions(isInline: true, selfClosing: false)
		);
	}

	public function wbr(HtmlGlobalAttributes $attributes): HtmlElement
	{
		return new HtmlElement(
			'wbr',
			$attributes,
			new NoneContent(),
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
