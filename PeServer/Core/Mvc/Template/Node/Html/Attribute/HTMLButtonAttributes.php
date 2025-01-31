<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLButtonAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	disabled?: null,
	 * 	form?: non-empty-string,
	 * 	formaction?: non-empty-string,
	 * 	formenctype?: "application/x-www-form-urlencoded"|"multipart/form-data"|"text/plain",
	 * 	formmethod?: "post"|"get"|"dialog",
	 * 	formnovalidate?: null,
	 * 	formtarget?: non-empty-string|"_self"|"_blank"|"_parent"|"_top",
	 * 	popovertarget?: non-empty-string,
	 * 	popovertargetaction?: "hide"|"show"|"toggle",
	 * 	type?: "submit"|"reset"|"button",
	 * 	value?: int|bool|string,
	 * }&HtmlTagAttributeAlias $attributes,
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
