<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\CoreError;

/**
 * 標準値。
 *
 * @deprecated いらんなこれ。生値でいいわ。気が向いたら消す。。。。消さんでもいいかなぁ。。。わからん。
 */
abstract class DefaultValue
{
	#region define

	/** 空文字列。 */
	public const EMPTY_STRING = '';

	/** 見つからない系 */
	public const NOT_FOUND_INDEX = -1;

	#endregion
}
