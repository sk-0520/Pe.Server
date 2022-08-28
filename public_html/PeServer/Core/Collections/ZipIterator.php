<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CallbackTypeError;

/**
 * zip イテレータ。
 *
 * @template TKey of array-key
 * @template TValue1
 * @template TValue2
 * @template TResult
 * @implements Iterator<TKey,TResult>
 */
class ZipIterator implements Iterator
{
	#region variable

	/**
	 * @var Iterator[]
	 */
	private array $iterators;

	#endregion

	/**
	 * 生成。
	 *
	 * @param Iterator $first
	 * @phpstan-param Iterator<TKey,TValue1> $first
	 * @param Iterator $second
	 * @phpstan-param Iterator<array-key,TValue2> $second
	 * @param mixed $callback
	 * @phpstan-param callable(array{0:TValue1,1:TValue2},TKey):(TResult) $callback
	 */
	public function __construct(Iterator $first, Iterator $second, private mixed $callback)
	{
		if (!is_callable($callback)) { //@phpstan-ignore-line phpstan-param callable
			throw new CallbackTypeError('$callback');
		}

		$this->iterators = [
			$first,
			$second,
		];
	}

	#region Iterator

	public function rewind(): void
	{
		foreach ($this->iterators as $iterator) {
			$iterator->rewind();
		}
	}

	public function key(): mixed
	{
		return $this->iterators[0]->key();
	}

	public function current(): mixed
	{
		$items = array_map(fn ($i) => $i->current(), $this->iterators);
		return call_user_func($this->callback, $items, $this->key());
	}

	public function next(): void
	{
		foreach ($this->iterators as $iterator) {
			$iterator->next();
		}
	}

	public function valid(): bool
	{
		foreach ($this->iterators as $iterator) {
			if (!$iterator->valid()) {
				return false;
			}
		}

		return true;
	}

	#endregion
}
