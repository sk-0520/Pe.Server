<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLImageAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	alt?: non-empty-string,
	 * 	decoding?: "sync"|"async"|"auto",
	 * 	crossorigin?: globa-alias-cross-origin,
	 * 	elementtiming?: non-empty-string,
	 * 	fetchpriority?: "high"|"low"|"auto",
	 * 	height?: int,
	 * 	ismap?: null,
	 * 	loading?: "eager"|"lazy",
	 * 	referrerpolicy?: globa-alias-referrer-policy,
	 * 	sizes?: non-empty-string,
	 * 	src?: non-empty-string,
	 * 	srcset?: non-empty-string,
	 * 	width?: int,
	 * 	usemap?: string,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
