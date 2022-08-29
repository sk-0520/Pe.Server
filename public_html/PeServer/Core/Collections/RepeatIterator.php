<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;

/**
 * repeat イテレータ。
 *
 * @template TValue
 * @implements Iterator<UnsignedIntegerAlias,TValue>
 */
class RepeatIterator implements Iterator
{
	#region variable

	/** @phpstan-var UnsignedIntegerAlias */
	private int $key = 0;

	#endregion

	/**
	 * 生成。
	 *
	 * @param mixed $value
	 * @param int $count
	 * @phpstan-param UnsignedIntegerAlias $count
	 */
	public function __construct(
		private mixed $value,
		private int $count
	) {
	}

	#region Iterator

	public function rewind(): void
	{
		$this->key = 0;
	}

	/**
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function key(): mixed
	{
		return $this->key;
	}

	/**
	 * @phpstan-return TValue
	 */
	public function current(): mixed
	{
		return $this->value;
	}

	public function next(): void
	{
		$this->key += 1;
	}

	public function valid(): bool
	{
		return $this->key < $this->count;
	}

	#endregion
}
