<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLTextAreaAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	autocomplete?: globa-alias-autocomplete,
	 * 	autofocus?: null,
	 * 	cols?: non-negative-int,
	 * 	dirname?: non-empty-string,
	 * 	disabled?: null,
	 * 	form?: non-empty-string,
	 * 	maxlength?: positive-int,
	 * 	minlength?: non-negative-int,
	 * 	placeholder?: string,
	 * 	readonly?: null,
	 * 	required?: null,
	 * 	rows?: non-negative-int,
	 * 	spellcheck?: bool|"default",
	 * 	wrap?: "hard"|"soft",
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
