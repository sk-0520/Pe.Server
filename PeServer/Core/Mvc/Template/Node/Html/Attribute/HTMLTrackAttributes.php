<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLTrackAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	default?: null,
	 * 	kind?: "subtitles"|"captions"|"descriptions"|"chapters"|"metadata",
	 * 	label?: non-empty-string,
	 * 	src?: null,
	 * 	srclang?: globa-alias-rfc-5646,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
