<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLAnchorAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	download?: null,
	 * 	href?: non-empty-string,
	 * 	hreflang?: globa-alias-rfc-5646,
	 * 	ping?: non-empty-string,
	 * 	referrerpolicy?: globa-alias-referrer-policy,
	 * 	rel?: non-empty-string,
	 * 	target?:non-empty-string|"_self"|"_blank"|"_parent"|"_top"|"_unfencedTop",
	 * 	type?: non-empty-string,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
