<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use TypeError;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\TypeUtility;

/**
 * 一次元配列。
 *
 * @template TValue
 * @phpstan-extends TypeArrayBase<non-negative-int,TValue>
 */
class Vector extends TypeArrayBase
{
	/**
	 * 生成。
	 *
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @param array $items
	 * @phpstan-param array<array-key,TValue> $items
	 * @param bool $useValues
	 * @param bool $isNullable
	 */
	public function __construct(string $type, ?array $items, bool $useValues, bool $isNullable)
	{
		parent::__construct($type, $isNullable);

		if (!Arr::isNullOrEmpty($items)) {
			$this->addRange($items, $useValues);
		}
	}

	#region function

	/**
	 * 配列から生成。
	 *
	 * @template TTValue
	 * @param array $items 配列。
	 * @phpstan-param non-empty-array<TTValue> $items
	 * @param bool $useValues
	 * @param bool $isNullable
	 * @return self
	 * @phpstan-return self<TTValue>
	 */
	public static function create(array $items, bool $useValues = true, bool $isNullable = false): self
	{
		if (Arr::isNullOrEmpty($items)) {
			throw new ArgumentException('$items');
		}

		$firstKey = Arr::getFirstKey($items);
		$firstValue = $items[$firstKey];

		$type = TypeUtility::getType($firstValue);
		return new self($type, $items, $useValues, $isNullable);
	}

	/**
	 * 空データの生成。
	 *
	 * @template TTValue
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @param bool $isNullable
	 * @return self
	 * @phpstan-return self<TTValue>
	 */
	public static function empty(string $type, bool $isNullable = false): self //@phpstan-ignore-line わかんね
	{
		return new self($type, [], false, $isNullable);
	}

	protected function throwIfInvalidOffset(mixed $offset): void
	{
		if ($offset === null) {
			throw new TypeError('$offset: null');
		}

		if (!is_int($offset)) {
			throw new TypeError('$offset: ' . gettype($offset));
		}
	}

	/**
	 * 追加。
	 *
	 * @param mixed $value
	 * @phpstan-param TValue $value
	 * @return self
	 * @phpstan-return self<TValue>
	 */
	public function add(mixed $value): self
	{
		$this->validateType($value);

		$this->items[] = $value;

		return $this;
	}

	/**
	 * 追加。
	 *
	 * @param array $items
	 * @phpstan-param array<array-key,TValue> $items
	 * @param bool $useValues
	 * @phpstan-return self<TValue>
	 */
	public function addRange(array $items, bool $useValues = true): self
	{
		if ($useValues) {
			$items = Arr::getValues($items);
		} elseif (!Arr::isList($items)) {
			throw new ArgumentException('$items');
		}

		foreach ($items as $key => $value) {
			$this->validateType($value);
		}

		/** @phpstan-var array<non-negative-int,TValue> $items */
		$this->items = array_merge($this->items, $items);

		return $this;
	}

	#endregion

	#region TypeArrayBase

	/**
	 * @param int $offset
	 * @phpstan-param non-negative-int $offset
	 */
	public function offsetExists(mixed $offset): bool
	{
		$this->throwIfInvalidOffset($offset);

		return isset($this->items[$offset]);
	}
	/**
	 * @param int $offset
	 * @phpstan-param non-negative-int $offset
	 * @phpstan-return TValue $value
	 */
	public function offsetGet(mixed $offset): mixed
	{
		$this->throwIfInvalidOffset($offset);

		if (!isset($this->items[$offset])) {
			throw new IndexOutOfRangeException('$offset: ' . $offset);
		}

		return $this->items[$offset];
	}
	/**
	 * @param int|null $offset
	 * @phpstan-param non-negative-int|null $offset
	 * @phpstan-param TValue $value
	 * @throws IndexOutOfRangeException
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			$this->add($value);
			return;
		}

		if (!isset($this->items[$offset])) {
			throw new IndexOutOfRangeException('$offset: ' . $offset);
		}

		$this->items[$offset] = $value;
	}
	/**
	 * @param int $offset
	 * @phpstan-param non-negative-int $offset
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->throwIfInvalidOffset($offset);

		if ($offset !== $this->count() - 1) {
			throw new IndexOutOfRangeException('$offset: ' . $offset);
		}

		unset($this->items[$offset]);
	}

	#endregion
}
