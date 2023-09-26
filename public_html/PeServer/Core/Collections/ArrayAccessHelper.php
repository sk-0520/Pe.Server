<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\TypeUtility;
use TypeError;

abstract class ArrayAccessHelper
{
	#region function

	/**
	 * ArrayAccess::offsetExists で UnsignedIntegerAlias に限定
	 *
	 * @param mixed $offset
	 * @return bool $offsetが有効
	 * @phpstan-assert-if-true UnsignedIntegerAlias $offset
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
	 * ArrayAccess::offsetGet で UnsignedIntegerAlias に限定(失敗時は例外)
	 *
	 * @param mixed $offset
	 * @throws TypeError 型がもうダメ。
	 * @throws IndexOutOfRangeException 範囲外。
	 * @phpstan-assert UnsignedIntegerAlias $offset
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
