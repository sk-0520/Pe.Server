<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLScriptAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string> $attributes
	 * @phpstan-param array{
	 * 	async?: empty-string,
	 * 	blocking?: "render",
	 * 	crossorigin?: empty-string,
	 * 	fetchpriority?: "high"|"low"|"auto",
	 * 	integrity? : empty-string,
	 * 	nomodule? : empty-string,
	 * 	nonce?: empty-string,
	 * 	referrerpolicy?: "no-referrer"|"no-referrer-when-downgrade"|"origin"|"origin-when-cross-origin"|"same-origin"|"strict-origin"|"strict-origin-when-cross-origin"|"unsafe-url"
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
