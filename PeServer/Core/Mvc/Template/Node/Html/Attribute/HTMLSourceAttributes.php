<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLSourceAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	type?: non-empty-string,
	 * 	src?: non-empty-string,
	 * 	srcset?: non-empty-string,
	 * 	sizes?: non-empty-string,
	 * 	media?: MediaQueryAlias,
	 * 	height?: int,
	 * 	width?: int,
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
