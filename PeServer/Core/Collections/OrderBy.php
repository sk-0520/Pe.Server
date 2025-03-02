<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

/**
 * 列挙順序。
 */
enum OrderBy: int
{
	#region define

	/**
	 * 昇順。
	 */
	case Ascending = -1;
	/**
	 * 降順。
	 */
	case Descending = +1;

	#endregion
}
