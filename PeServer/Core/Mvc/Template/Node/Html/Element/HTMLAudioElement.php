<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAudioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLAudioElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAudioAttributes $attributes
	 * @param HtmlContent $content
	 * @param Props $props
	 */
	public function __construct(
		HTMLAudioAttributes $attributes = new HTMLAudioAttributes(),
		HtmlContent $content = new HtmlContent(),
		Props $props = new Props()
	) {
		parent::__construct(
			"audio",
			$attributes,
			$content,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
