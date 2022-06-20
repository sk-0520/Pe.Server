<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 一次元配列。
 *
 * @template TValue
 */
class ListArray
{
	/**
	 * アイテム一覧。
	 *
	 * @var array<TValue>
	 */
	private array $items;

	/**
	 * 生成。
	 *
	 * @param array<TValue> $items
	 */
	public function __construct(?array $items = null)
	{
		$this->items = $items ?? [];
	}

	/**
	 * 現在要素数を取得。
	 *
	 * @return integer
	 */
	public function getCount(): int
	{
		return count($this->items);
	}

	/**
	 * 配列データを取得。
	 *
	 * @return array<TValue>
	 */
	public function getArray(): array
	{
		return $this->items;
	}

	/**
	 * Undocumented function
	 *
	 * @param TValue $value
	 * @return ListArray<TValue>
	 */
	public function add($value): ListArray
	{
		$this->items[] = $value;

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param array<TValue> $items
	 * @return ListArray<TValue>
	 */
	public function addRange(array $items): ListArray
	{
		$this->items = array_merge($this->items, $items);

		return $this;
	}
}
