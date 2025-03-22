<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use TypeError;
use PeServer\Core\Collections\Arr;
use PeServer\Core\TypeUtility;

/**
 * 値の型限定基底配列。
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayAccess<TKey,TValue>
 * @implements IteratorAggregate<TKey,TValue>
 */
abstract class TypeArrayBase implements ArrayAccess, Countable, IteratorAggregate
{
	#region variable

	/**
	 * アイテム一覧。
	 *
	 * @var array
	 * @phpstan-var array<TKey,TValue>
	 */
	protected array $items = [];

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @param bool $isNullable NULL を許容するか。
	 */
	protected function __construct(
		protected string $type,
		protected bool $isNullable = false
	) {
		//NOP
	}

	#region function

	/**
	 * 配列データを取得。
	 *
	 * @return array
	 * @phpstan-return array<TKey,TValue>
	 */
	public function getArray(): array
	{
		return $this->items;
	}

	abstract protected function throwIfInvalidOffset(mixed $offset): void;

	protected function validateType(mixed $value): void
	{
		if ($value === null) {
			if (!$this->isNullable) {
				throw new TypeError('not null');
			}
		}

		$type = TypeUtility::getType($value);

		if ($this->type === $type) {
			return;
		}

		if (is_object($value)) {
			if (!is_a($value, $this->type)) {
				throw new TypeError('$value');
			}
		} else {
			throw new TypeError('$value');
		}
	}

	#endregion

	#region Countable

	/** @phpstan-return non-negative-int */
	public function count(): int
	{
		return Arr::getCount($this->items);
	}

	#endregion

	#region IteratorAggregate

	/**
	 * @phpstan-return Traversable<TKey, TValue>
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->items);
	}

	#endregion
}
