<?php

declare(strict_types=1);

namespace PeServer\Core\Collection;

use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\TypeUtility;
use TypeError;

abstract class ArrayAccessHelper
{
	#region function

	/**
	 * ArrayAccess::offsetExists で non-negative-int に限定
	 *
	 * @param mixed $offset
	 * @return bool $offsetが有効
	 * @phpstan-assert-if-true non-negative-int $offset
	 * @phpstan-pure
	 */
	public static function offsetExistsUInt(mixed $offset): bool
	{
		if (!is_int($offset)) {
			return false;
		}

		if ($offset < 0) {
			return false;
		}

		return true;
	}

	/**
	 * ArrayAccess::offsetGet で non-negative-int に限定(失敗時は例外)
	 *
	 * @param mixed $offset
	 * @throws TypeError 型がもうダメ。
	 * @throws IndexOutOfRangeException 範囲外。
	 * @phpstan-assert non-negative-int $offset
	 */
	public static function offsetGetUInt(mixed $offset): void
	{
		if (!is_int($offset)) {
			throw new TypeError(TypeUtility::getType($offset));
		}

		if ($offset < 0) {
			throw new IndexOutOfRangeException((string)$offset);
		}
	}

	#endregion
}
