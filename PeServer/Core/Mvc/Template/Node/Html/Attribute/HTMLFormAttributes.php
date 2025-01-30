<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLFormAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	accept-charset?: non-empty-string,
	 * 	autocomplete?: "on"|"off",
	 * 	rel?: non-empty-string,
	 * 	action?: non-empty-string,
	 * 	enctype?: "application/x-www-form-urlencoded"|"multipart/form-data"|"text/plain",
	 * 	method?: "post"|"get"|"dialog",
	 * 	novalidate?: null,
	 * 	target?: "_self"|"_blank"|"_parent"|"_top"|"_unfencedTop",
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
