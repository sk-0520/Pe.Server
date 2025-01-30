<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLObjectAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	data?: non-empty-string,
	 * 	form?: non-empty-string,
	 * 	height?: int,
	 * 	type?: non-empty-string,
	 * 	usemap?: non-empty-string,
	 * 	width?: int,
	 * }&HtmlTagAttributeAlias $attributes
	 * @phpstan-ignore missingType.iterableValue, parameter.unresolvableType
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
