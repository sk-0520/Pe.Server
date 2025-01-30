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
	 * 	allow?: PermissionsPolicyAlias,
	 * 	height?: 150,
	 * 	loading?: "eager"|"lazy",
	 * 	referrerpolicy?: ReferrerPolicyAlias,
	 * 	sandbox?: non-empty-string,
	 * 	src?: non-empty-string,
	 * 	srcdoc?: non-empty-string,
	 * 	width?: int,
	 * }&HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
