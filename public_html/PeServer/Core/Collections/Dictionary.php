<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \TypeError;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Collections\TypeArrayBase;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\TypeUtility;

/**
 * 連想配列。
 *
 * @template TValue
 * @extends TypeArrayBase<string,TValue>
 */
class Dictionary extends TypeArrayBase
{
	/**
	 * 生成。
	 *
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @param array $type
	 * @phpstan-param array<string,TValue> $map
	 */
	public function __construct(string $type, array $map)
	{
		parent::__construct($type);
		$this->items = $map;
	}

	/**
	 * 配列から生成。
	 *
	 * @template TTValue
	 * @param array $map 配列。
	 * @phpstan-param non-empty-array<string,TTValue> $map
	 * @return Dictionary
	 * @phpstan-return Dictionary<TTValue>
	 */
	public static function create(array $map): Dictionary
	{
		if (ArrayUtility::isNullOrEmpty($map)) {
			throw new ArgumentException('$map');
		}

		$firstKey = ArrayUtility::getFirstKey($map);
		if(!is_string($firstKey)) {
			throw new TypeError('$offset: ' . $firstKey);
		}
		$firstValue = $map[$firstKey];

		$type = TypeUtility::getType($firstValue);

		return new self($type, $map);
	}

	protected function throwIfInvalidOffset(mixed $offset): void
	{
		if (is_null($offset)) {
			throw new TypeError('$offset: null');
		}

		if (!is_string($offset)) {
			throw new TypeError('$offset: ' . gettype($offset));
		}
	}


	// ArrayAccess --------------------------------------

	/**
	 * @param string $offset
	 * @phpstan-param string $offset
	 */
	public function offsetExists(mixed $offset): bool
	{
		$this->throwIfInvalidOffset($offset);

		return isset($this->items[$offset]);
	}
	/**
	 * @param string $offset
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
	 * @param string|null $offset
	 * @phpstan-param TValue $value
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			throw new NotSupportedException();
		}

		$this->items[$offset] = $value;
	}
	/**
	 * @param string $offset
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->throwIfInvalidOffset($offset);

		unset($this->items[$offset]);
	}
}
