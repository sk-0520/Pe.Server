<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HTMLTableHeaderCellAttributes extends HtmlAttributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string|null> $attributes
	 * @phpstan-param array{
	 * 	abbr?: non-empty-string,
	 * 	colspan?: non-negative-int,
	 * 	headers?: non-empty-string,
	 * 	rowspan?: non-negative-int,
	 * 	scope?: "row"|"col"|"rowgroup"|"colgroup",
	 * }&globa-alias-html-tag-attribute $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
