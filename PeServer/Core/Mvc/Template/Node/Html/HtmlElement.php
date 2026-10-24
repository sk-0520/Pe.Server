<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;

class HtmlElement extends Element
{
	public function __construct(
		string $tagName,
		HtmlGlobalAttributes $attributes,
		Content $content,
		ElementOptions $options
	) {
		parent::__construct($tagName, $attributes, $content, $options);
	}
}
