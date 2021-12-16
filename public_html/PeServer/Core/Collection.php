<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Traversable;
use \ArrayIterator;
use \IteratorAggregate;

/**
 * LINQ 的なことがしたいけど PHP のイテレータ処理知らんからとりあえず適当実装。
 */
class Collection implements IteratorAggregate // @phpstan-ignore-line
{
	/** @var array */
	private $items; // @phpstan-ignore-line

	private function __construct(array $items) // @phpstan-ignore-line
	{
		$this->items = $items;
	}

	/**
	 * Undocumented function
	 *
	 * @return Traversable
	 */
	public function getIterator(): Traversable // @phpstan-ignore-line
	{
		return new ArrayIterator($this->items);
	}

	public function toArray(): array // @phpstan-ignore-line
	{
		return array_values($this->items);
	}

	/**
	 * 開始。
	 *
	 * new でメソッドをつなげられん。
	 *
	 * @param array $items
	 * @return Collection
	 */
	public static function from(array $items): Collection // @phpstan-ignore-line
	{
		return new Collection($items);
	}

	// ----------------------------------------------------

	/**
	 * Undocumented function
	 *
	 * @param callable $callback
	 * @return Collection
	 */
	function where(callable $callback): Collection // @phpstan-ignore-line
	{
		return self::from(array_filter($this->items, $callback));
	}
}
