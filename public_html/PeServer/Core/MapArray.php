<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 連想配列。
 *
 * @template TKey
 * @template TValue
 */
class MapArray
{
	/**
	 * アイテム一覧。
	 *
	 * @var array<TKey,TValue>
	 */
	public array $items = [];

	/**
	 * 生成。
	 *
	 * @param array<TKey,TValue> $items
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
	 * @return array<TKey,TValue>
	 */
	public function getArray(): array
	{
		return $this->items;
	}
}
