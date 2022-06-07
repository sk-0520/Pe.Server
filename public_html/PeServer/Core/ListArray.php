<?php

declare(strict_types=1);

namespace PeServer\Core;

class ListArray
{
	/**
	 * Undocumented variable
	 *
	 * @var array<mixed>
	 */
	private array $items;

	/**
	 * Undocumented function
	 *
	 * @param array<mixed> $items
	 */
	public function __construct(?array $items = null)
	{
		$this->items = $items ?? [];
	}

	/**
	 * Undocumented function
	 *
	 * @param mixed $value
	 * @return ListArray
	 */
	public function add(mixed $value): ListArray
	{
		$this->items[] = $value;

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param array<mixed> $items
	 * @return ListArray
	 */
	public function addRange(array $items): ListArray
	{
		$this->items = array_merge($this->items, $items);

		return $this;
	}

	/**
	 * 配列データを取得。
	 *
	 * @return array<mixed>
	 */
	public function getArray(): array
	{
		return $this->items;
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
}
