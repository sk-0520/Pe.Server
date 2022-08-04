<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \ArrayIterator;
use \Iterator;
use \IteratorAggregate;
use \IteratorIterator;
use \Traversable;

abstract class CollectionUtility
{
	/**
	 * イテレータに変換。
	 *
	 * @template TKey of array-key
	 * @template TValue
	 * @param Traversable|array<mixed>|callable $sequence
	 * @phpstan-param Traversable<TKey,TValue>|array<TKey,TValue>|callable():\Generator<TKey,TValue> $sequence
	 * @return Iterator
	 * @phpstan-return Iterator<TKey,TValue>
	 */
	public static function toIterator(Traversable|array|callable $sequence): Iterator
	{
		if ($sequence instanceof IteratorAggregate) {
			return new IteratorIterator($sequence);
		}
		if ($sequence instanceof Iterator) {
			return $sequence;
		}
		if(is_callable($sequence)) {
			return new GeneratorIterator($sequence);
		}

		assert(is_array($sequence));
		return new ArrayIterator($sequence);
	}

	/**
	 * `Traversable` を配列に変換。
	 *
	 * @template TKey of array-key
	 * @template TValue
	 * @param Traversable $traverse
	 * @phpstan-param Traversable<TKey,TValue> $traverse
	 * @param bool $preserveKeys
	 * @return array
	 * @phpstan-return array<TKey,TValue>
	 */
	public static function toArray(Traversable $traverse, bool $preserveKeys): array
	{
		return iterator_to_array($traverse, $preserveKeys);
	}
}
