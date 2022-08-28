<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;

/**
 * take イテレータ。
 *
 * @template TKey of array-key
 * @template TValue
 * @implements Iterator<TKey,TValue>
 */
class TakeIterator implements Iterator
{
	#region variable

	private int $position = 0;

	#endregion

	/**
	 * 生成
	 *
	 * @param Iterator $iterator
	 * @param int $count
	 * @phpstan-param UnsignedIntegerAlias $count
	 */
	public function __construct(
		private Iterator $iterator,
		/** @readonly */
		private int $count
	) {
	}

	#region Iterator

	public function rewind(): void
	{
		$this->position = 0;
		$this->iterator->rewind();
	}


	public function key(): mixed
	{
		return $this->iterator->key();
	}

	public function current(): mixed
	{
		return $this->iterator->current();
	}

	public function next(): void
	{
		$this->position += 1;
		if ($this->valid()) {
			$this->iterator->next();
		}
	}

	public function valid(): bool
	{
		if ($this->position < $this->count) {
			return $this->iterator->valid();
		}

		return false;
	}

	#endregion
}
