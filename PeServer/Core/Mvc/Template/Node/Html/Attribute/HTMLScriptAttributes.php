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
	 * 	crossorigin?: empty-string,
	 * 	fetchpriority?: "high"|"low"|"auto",
	 * 	integrity? : empty-string,
	 * 	nomodule? : empty-string,
	 * 	nonce?: empty-string,
	 * 	referrerpolicy?: ReferrerPolicyAlias
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
