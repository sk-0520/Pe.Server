<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;

class HTMLHtmlAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string> $attributes
	 * @phpstan-param array{xmlns?: non-empty-string}&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
