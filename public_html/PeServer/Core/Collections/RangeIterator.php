<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;

/**
 * range イテレータ。
 *
 * @template TValue
 * @implements Iterator<int,TValue>
 */
class RangeIterator implements Iterator
{
	private int $key = 0;
	private int $current;

	public function __construct(
		private int $start,
		private int $count
	) {
		$this->current = $start;
	}

	public function rewind(): void
	{
		$this->key = 0;
		$this->current = $this->start;
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
		return $this->current;
	}

	public function next(): void
	{
		$this->key += 1;
		$this->current += 1;
	}

	public function valid(): bool
	{
		return $this->current() < ($this->start + $this->count);
	}
}
