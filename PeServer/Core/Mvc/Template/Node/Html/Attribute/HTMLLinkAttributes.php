<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLLinkAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	as?: "audio"|"document"|"embed"|"fetch"|"font"|"image"|"object"|"script"|"style"|"track"|"video"|"worker",
	 * 	crossorigin?: globa-alias-cross-origin,
	 * 	disabled?: bool|null|"false",
	 * 	fetchpriority?: "high"|"low"|"auto",
	 * 	href?: non-empty-string,
	 * 	hreflang?: globa-alias-rfc-5646,
	 * 	imagesizes?: non-empty-string,
	 * 	imagesrcset?: non-empty-string,
	 * 	integrity?: non-empty-string,
	 * 	media?: globa-alias-media-query,
	 * 	referrerpolicy?: globa-alias-referrer-policy,
	 * 	rel?: non-empty-string,
	 * 	sizes?: non-empty-string,
	 * 	type?: non-empty-string,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
