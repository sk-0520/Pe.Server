<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use EmptyIterator;
use Iterator;
use PeServer\Core\Collections\TraverseUtility;
use PeServer\Core\Throws\CallbackTypeError;

/**
 * selectMany イテレータ。
 *
 * @template TKey of array-key
 * @template TValue
 * @template TResult
 * @implements Iterator<TKey,TResult>
 */
class SelectManyIterator implements Iterator
{
	#region variable

	/** @phpstan-var Iterator<TKey,Iterator<TKey,TValue>> */
	private Iterator $outerIterator;
	/** @phpstan-var Iterator<TKey,TValue> */
	private Iterator $innerIterator;

	#endregion

	/**
	 * 生成。
	 *
	 * @param Iterator $iterator
	 * @phpstan-param Iterator<TKey,Iterator<TKey,TValue>> $iterator
	 * @param mixed $callback
	 * @phpstan-param callable(TValue,TKey):(TResult) $callback
	 */
	public function __construct(
		Iterator $iterator,
		private mixed $callback
	) {
		if (!is_callable($callback)) { //@phpstan-ignore-line phpstan-param callable
			throw new CallbackTypeError('$callback');
		}

		$this->outerIterator = $iterator;
		$this->innerIterator = new EmptyIterator();
	}

	#region Iterator

	public function rewind(): void
	{
		$this->outerIterator->rewind();
		if ($this->outerIterator->valid()) {
			$this->innerIterator = TraverseUtility::toIterator($this->outerIterator->current());
		}
	}

	/**
	 * @return int
	 */
	public function key(): mixed
	{
		return $this->innerIterator->key();
	}

	/**
	 * @phpstan-return TResult
	 */
	public function current(): mixed
	{
		return call_user_func($this->callback, $this->innerIterator->current(), $this->innerIterator->key());
	}

	public function next(): void
	{
		if ($this->innerIterator->valid()) {
			$this->innerIterator->next();
			if ($this->innerIterator->valid()) {
				return;
			}
		}

		if ($this->outerIterator->valid()) {
			$this->outerIterator->next();
			if ($this->outerIterator->valid()) {
				$this->innerIterator = TraverseUtility::toIterator($this->outerIterator->current());
			}
		}
	}

	public function valid(): bool
	{
		if ($this->innerIterator->valid()) {
			return true;
		}

		return $this->outerIterator->valid();
	}

	#endregion
}
