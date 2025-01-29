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
	 * 	hreflang?: RFC5646Alias,
	 * 	ping?: non-empty-string,
	 * 	referrerpolicy?: "no-referrer"|"no-referrer-when-downgrade"|"origin"|"origin-when-cross-origin"|"same-origin"|"strict-origin"|"strict-origin-when-cross-origin"|"unsafe-url",
	 * 	rel?: non-empty-string,
	 * 	target?:non-empty-string|"_self"|"_blank"|"_parent"|"_top"|"_unfencedTop",
	 * 	type?: non-empty-string,
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
