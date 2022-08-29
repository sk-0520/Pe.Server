<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;

/**
 * range イテレータ。
 *
 * @implements Iterator<UnsignedIntegerAlias,int>
 */
class RangeIterator implements Iterator
{
	#region variable

	/** @phpstan-var UnsignedIntegerAlias */
	private int $key = 0;
	private int $current;

	#endregion

	/**
	 * 生成
	 *
	 * @param int $start
	 * @param int $count
	 * @phpstan-param UnsignedIntegerAlias $count
	 */
	public function __construct(
		private int $start,
		private int $count
	) {
		$this->current = $start;
	}

	#region Iterator

	public function rewind(): void
	{
		$this->key = 0;
		$this->current = $this->start;
	}

	/**
	 * @phpstan-return UnsignedIntegerAlias
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

	#endregion
}
