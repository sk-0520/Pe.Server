<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

/**
 * 列挙順序。
 */
abstract class OrderBy
{
	#region define

	/**
	 * 昇順。
	 */
	public const ASCENDING  = -1;
	/**
	 * 降順。
	 */
	public const DESCENDING  = +1;

	#endregion
}
