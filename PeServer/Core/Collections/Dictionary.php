<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use TypeError;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\TypeArrayBase;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\TypeUtility;

/**
 * 連想配列(キーは文字列限定)。
 *
 * @template TValue
 * @extends TypeArrayBase<string,TValue>
 */
class Dictionary extends TypeArrayBase
{
	/**
	 * 生成。
	 *
	 * @param class-string|TypeUtility::TYPE_* $type
	 * @param array $map
	 * @phpstan-param array<string,TValue> $map
	 * @param bool $isNullable
	 */
	public function __construct(string $type, array $map, bool $isNullable)
	{
		parent::__construct($type, $isNullable);

		foreach ($map as $key => $value) {
			$this->validateType($value);
			$this->items[$key] = $value;
		}
	}

	#region function

	/**
	 * 配列から生成。
	 *
	 * @template TTValue
	 * @param array $map 配列。
	 * @phpstan-param non-empty-array<string,TTValue> $map
	 * @param bool $isNullable
	 * @return self
	 * @phpstan-return self<TTValue>
	 */
	public static function create(array $map, bool $isNullable = false): self
	{
		// @phpstan-ignore staticMethod.impossibleType
		if (Arr::isNullOrEmpty($map)) {
			throw new ArgumentException('$map');
		}

		$firstKey = Arr::getFirstKey($map);
		$firstValue = $map[$firstKey];

		$type = TypeUtility::getType($firstValue);

		return new self($type, $map, $isNullable);
	}

	/**
	 * 空データの生成。
	 *
	 * @template TTValue
	 * @param class-string|TypeUtility::TYPE_* $type
	 * @param bool $isNullable
	 * @return self
	 * @phpstan-return self<TTValue>
	 */
	public static function empty(string $type, bool $isNullable = false): self //@phpstan-ignore-line TTValue
	{
		return new self($type, [], $isNullable);
	}

	protected function throwIfInvalidOffset(mixed $offset): void
	{
		if ($offset === null) {
			throw new TypeError('$offset: null');
		}

		if (!is_string($offset)) {
			throw new TypeError('$offset: ' . gettype($offset));
		}
	}

	#endregion


	#region TypeArrayBase

	/**
	 * @param string $offset
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
			throw new KeyNotFoundException('$offset: ' . $offset);
		}

		return $this->items[$offset];
	}
	/**
	 * @param string|null $offset
	 * @phpstan-param TValue $value
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		ArgumentNullException::throwIfNull($offset, '$offset');

		$this->validateType($value);

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

	#endregion
}
