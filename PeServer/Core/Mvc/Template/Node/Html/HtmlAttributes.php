<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;

class HtmlAttributes extends Attributes
{
	/**
	 * 生成。
	 *
	 * @param array $attributes
	 * @phpstan-param HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
