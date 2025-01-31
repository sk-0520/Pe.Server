<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Element;

use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextContent;

class HTMLElement extends Element
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $tagName
	 * @param HtmlAttributes $attributes
	 * @param HtmlContent|NoneContent|TextContent $content
	 * @param Props $props
	 * @param HtmlElementOptions $options
	 */
	public function __construct(
		string $tagName,
		HtmlAttributes $attributes,
		HtmlContent|NoneContent|TextContent $content,
		Props $props,
		HtmlElementOptions $options
	) {
		parent::__construct($tagName, $attributes, $content, $props, $options);
	}
}
