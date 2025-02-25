<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLAreaAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	alt?: string,
	 * 	coords?: "rect"|"circle"|"poly",
	 * 	download?: null,
	 * 	href?: non-empty-string,
	 * 	hreflang?: globa-alias-rfc-5646,
	 * 	ping?: non-empty-string,
	 * 	rel?: non-empty-string,
	 * 	shape?: non-empty-string,
	 * 	target?:non-empty-string|"_self"|"_blank"|"_parent"|"_top",
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
