<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAudioAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;

class HTMLAudioElement extends HTMLElement
{
	/**
	 * 生成。
	 *
	 * @param HTMLAudioAttributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 */
	public function __construct(
		HTMLAudioAttributes $attributes = new HTMLAudioAttributes(),
		array $children = [],
		Props $props = new Props()
	) {
		parent::__construct(
			"audio",
			$attributes,
			$children,
			$props,
			HtmlElementOptions::block(false)
		);
	}
}
