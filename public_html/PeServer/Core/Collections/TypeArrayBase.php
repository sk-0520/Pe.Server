<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \ArrayAccess;
use \ArrayIterator;
use \Countable;
use \IteratorAggregate;
use \Traversable;
use \TypeError;
use PeServer\Core\ArrayUtility;
use PeServer\Core\TypeUtility;

/**
 * 値の型限定基底配列。
 *
 * @template TKey of array-key
 * @template TValue
 * @phpstan-implements ArrayAccess<TKey,TValue>
 * @phpstan-implements IteratorAggregate<TKey,TValue>
 */
abstract class TypeArrayBase implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * アイテム一覧。
	 *
	 * @var array
	 * @phpstan-var array<TKey,TValue>
	 */
	protected array $items = [];

	/**
	 * 生成。
	 *
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 */
	protected function __construct(
		protected string $type
	) {
	}

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

	protected abstract function throwIfInvalidOffset(mixed $offset): void;

	protected function isValidType(mixed $value): void
	{
		if (is_null($value)) {
			if (!TypeUtility::isNullable($this->type)) {
				throw new TypeError('$value');
			}
			return;
		}

		$type = TypeUtility::getType($value);

		if ($this->type === $type) {
			return;
		}

		if (is_object($value)) {
			if (!is_subclass_of($value, $this->type)) { //@phpstan-ignore-line
				throw new TypeError('$value');
			}
		} else {
			throw new TypeError('$value');
		}
	}


	// Countable --------------------------------------

	/** @phpstan-return UnsignedIntegerAlias */
	public function count(): int
	{
		return ArrayUtility::getCount($this->items);
	}

	// IteratorAggregate --------------------------------------
	/**
	 * @phpstan-return Traversable<TKey, TValue>
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->items);
	}
}
