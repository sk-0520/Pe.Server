<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLAudioAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	autoplay?: null,
	 * 	controls?: null,
	 * 	controlslist?: "nodownload"|"nofullscreen"|"noremoteplayback",
	 * 	crossorigin?: globa-alias-cross-origin,
	 * 	disableremoteplayback?: null,
	 * 	loop?: null,
	 * 	muted?: bool,
	 * 	preload?: "none"|"metadata"|"auto",
	 * 	src?: non-empty-string,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
