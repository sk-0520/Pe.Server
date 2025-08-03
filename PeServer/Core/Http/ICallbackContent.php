<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

/**
 * 応答本文出力機。
 */
interface ICallbackContent
{
	#region variable

	public const UNKNOWN = -1;

	#endregion

	#region function

	/**
	 * 出力処理。
	 *
	 * 自前で `echo` とかいい感じに。
	 */
	public function output(): void;

	/**
	 * 出力長。
	 *
	 * @return int 0以上の場合は決定された出力byte数。不明な場合は `UNKNOWN`。
	 * @phpstan-return non-negative-int|self::UNKNOWN
	 * @see ICallbackContent::UNKNOWN
	 */
	public function getLength(): int;

	#endregion
}
