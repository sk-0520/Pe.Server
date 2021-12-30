<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * アクションに対するオプション。
 */
interface IActionFilter
{
	/**
	 * Undocumented function
	 *
	 * @param FilterArgument $argument
	 * @return FilterResult
	 */
	public function filtering(FilterArgument $argument): FilterResult;
}
