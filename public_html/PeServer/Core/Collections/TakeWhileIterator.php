<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;
use PeServer\Core\Throws\ArgumentException;

/**
 * takeWhile イテレータ。
 *
 * @template TKey of array-key
 * @template TValue
 * @implements Iterator<TKey,TValue>
 */
class TakeWhileIterator implements Iterator
{
	private int $position = 0;

	/**
	 * 生成
	 *
	 * @param Iterator $iterator
	 * @param mixed $callback
	 * @phpstan-param callable(TValue,TKey):(bool) $callback
	 */
	public function __construct(
		private Iterator $iterator,
		private mixed $callback
	) {
		if (!is_callable($callback)) { //@phpstan-ignore-line phpstan-param callable
			throw new ArgumentException('$callback');
		}
	}

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
		$this->iterator->next();
	}

	public function valid(): bool
	{
		if(!$this->iterator->valid()) {
			return false;
		}

		return call_user_func($this->callback, $this->iterator->current(), $this->iterator->key());
	}
}
