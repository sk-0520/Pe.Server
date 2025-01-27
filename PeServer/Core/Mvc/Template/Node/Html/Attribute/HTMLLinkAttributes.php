<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLLinkAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string> $attributes
	 * @phpstan-param array{
	 * 	as?: "audio"|"document"|"embed"|"fetch"|"font"|"image"|"object"|"script"|"style"|"track"|"video"|"worker",
	 * 	crossorigin?: "anonymous"|"use-credentials",
	 * 	disabled?: bool|null|"false",
	 * 	fetchpriority?: "high"|"low"|"auto",
	 * 	href?: non-empty-string,
	 * 	hreflang?: RFC5646Alias,
	 * 	imagesizes?: non-empty-string,
	 * 	imagesrcset?: non-empty-string,
	 * 	integrity?: non-empty-string,
	 * 	media?: MediaQueryAlias,
	 * 	referrerpolicy?: "no-referrer"|"no-referrer-when-downgrade"|"origin"|"origin-when-cross-origin"|"unsafe-url",
	 * 	rel?: non-empty-string,
	 * 	sizes?: non-empty-string,
	 * 	type?: non-empty-string,
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
