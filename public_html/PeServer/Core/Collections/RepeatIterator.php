<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;

/**
 * repeat イテレータ。
 *
 * @template TValue
 * @implements Iterator<int,TValue>
 */
class RepeatIterator implements Iterator
{
	private int $key = 0;

	public function __construct(
		private mixed $value,
		private int $count
	) {
	}

	public function rewind(): void
	{
		$this->key = 0;
	}

	/**
	 * @return int
	 */
	public function key(): mixed
	{
		return $this->key;
	}

	/**
	 * @return int
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
}
