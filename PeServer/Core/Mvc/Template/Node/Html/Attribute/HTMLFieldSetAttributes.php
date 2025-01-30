<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLFieldSetAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	disabled?: null,
	 * 	form?: non-empty-string,
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
