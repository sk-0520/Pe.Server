<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Html\HTMLElement;

class Attr
{
	#region function

	/**
	 * HTML 属性。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{}&HtmlTagAttributeAlias $attributes
	 *
	 * @return HtmlAttributes
	 */
	public function html(array $attributes = []): HtmlAttributes
	{
		return new HtmlAttributes($attributes);
	}

	#endregion
}
