<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

/**  */
class HTMLInputAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	accept?: non-empty-string,
	 * 	alt?: non-empty-string,
	 * 	capture?: "user"|"environment",
	 * 	checked?: null,
	 * 	dirname?: non-empty-string,
	 * 	disabled?: null,
	 * 	form?: non-empty-string,
	 * 	formaction?: non-empty-string,
	 * 	formenctype?: "application/x-www-form-urlencoded"|"multipart/form-data"|"text/plain",
	 * 	formmethod?: "get"|"post"|"dialog",
	 * 	formnovalidate?: null,
	 * 	formtarget?: non-empty-string,
	 * 	height?: int,
	 * 	inputmode?: "none"|"text"|"tel"|"url"|"email"|"numeric"|"decimal"|"search",
	 * 	list?: non-empty-string,
	 * 	max?: int|float,
	 * 	maxlength?: int|float,
	 * 	min?: int|float,
	 * 	minlength?: int|float,
	 * 	multiple?: null,
	 * 	pattern?: non-empty-string,
	 * 	placeholder?: non-empty-string,
	 * 	popovertarget?: non-empty-string,
	 * 	popovertargetaction?: "hide"|"show"|"toggle",
	 * 	readonly?: null,
	 * 	required?: null,
	 * 	size?: int,
	 * 	src?: non-empty-string,
	 * 	step?: int|float,
	 * 	tabindex?: int,
	 * 	type?: "button"|"checkbox"|"color"|"date"|"datetime-local"|"email"|"file"|"hidden"|"image"|"month"|"number"|"password"|"radio"|"range"|"reset"|"search"|"submit"|"tel"|"text"|"time"|"url"|"week",
	 * 	value?: int|float|string,
	 * 	width?: int,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
