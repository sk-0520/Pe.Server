<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLVideoAttributes extends HtmlAttributes
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
	 * 	height?: non-negative-int,
	 * 	loop?: null,
	 * 	muted?: null,
	 * 	playsinline?: null,
	 * 	poster?: non-empty-string,
	 * 	preload?: "none"|"metadata"|"auto",
	 * 	src?: non-empty-string,
	 * 	width?: non-negative-int,
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
