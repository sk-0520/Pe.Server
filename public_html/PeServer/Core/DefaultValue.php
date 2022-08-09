<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\CoreError;

/**
 * 標準値。
 */
abstract class DefaultValue
{
	/** 空文字列。 */
	public const EMPTY_STRING = '';

	/** 見つからない系 */
	public const NOT_FOUND_INDEX = -1;
}
