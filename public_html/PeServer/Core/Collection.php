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
	/** @var array<mixed> */
	private array $items;

	/**
	 * Undocumented function
	 *
	 * @param array<mixed> $items
	 */
	private function __construct(array $items)
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

	/**
	 * 配列生成。
	 *
	 * @return array
	 */
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
	 * @param callable(mixed): bool $callback
	 * @return Collection
	 */
	function where(callable $callback): Collection // @phpstan-ignore-line
	{
		return self::from(array_filter($this->items, $callback));
	}

	/**
	 * Undocumented function
	 *
	 * @param callable|null $callback
	 * @return boolean
	 */
	public function any(?callable $callback = null): bool
	{
		if (is_null($callback)) {
			return 0 < count($this->items);
		}

		foreach ($this->items as $item) {
			if ($callback($item)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Undocumented function
	 *
	 * @param callable $callback
	 * @return boolean
	 */
	public function all(callable $callback): bool
	{
		if (count($this->items) === 0) {
			return true;
		}

		foreach ($this->items as $item) {
			if (!$callback($item)) {
				return false;
			}
		}

		return true;
	}
}
