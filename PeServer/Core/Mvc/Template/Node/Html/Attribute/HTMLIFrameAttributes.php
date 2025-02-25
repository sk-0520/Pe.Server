<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLIFrameAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	allow?: globa-alias-permissions-policy,
	 * 	height?: 150,
	 * 	loading?: "eager"|"lazy",
	 * 	referrerpolicy?: globa-alias-referrer-policy,
	 * 	sandbox?: non-empty-string,
	 * 	src?: non-empty-string,
	 * 	srcdoc?: non-empty-string,
	 * 	width?: int,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
