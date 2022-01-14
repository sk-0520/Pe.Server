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
	public function add($value): ListArray
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
		$this->items += $items;

		return $this;
	}
}
