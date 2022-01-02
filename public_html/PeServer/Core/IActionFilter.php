<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\FilterResult;
use PeServer\Core\FilterArgument;

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
