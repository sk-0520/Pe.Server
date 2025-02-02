<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDialogAttribute;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLDialogElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLDialogAttribute $attributes
	 * @param HtmlContent $content

	 */
	public function __construct(
		HTMLDialogAttribute $attributes = new HTMLDialogAttribute(),
		HtmlContent $content = new HtmlContent(),
	) {
		parent::__construct(
			"dialog",
			$attributes,
			$content,
			HtmlElementOptions::block(false)
		);
	}
}
