<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLScriptAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	async?: empty-string,
	 * 	blocking?: "render",
	 * 	crossorigin?: globa-alias-cross-origin,
	 * 	fetchpriority?: "high"|"low"|"auto",
	 * 	integrity? : empty-string,
	 * 	nomodule? : empty-string,
	 * 	nonce?: empty-string,
	 * 	referrerpolicy?: globa-alias-referrer-policy
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
