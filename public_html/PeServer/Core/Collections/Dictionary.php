<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \TypeError;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Collections\TypeArrayBase;
use PeServer\Core\Throws\ArgumentException;
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
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @param array $type
	 * @phpstan-param array<string,TValue> $map
	 */
	public function __construct(string $type, array $map)
	{
		parent::__construct($type);

		foreach ($map as $key => $value) {
			$this->isValidType($value);
			$this->items[$key] = $value;
		}
	}

	/**
	 * 配列から生成。
	 *
	 * @template TTValue
	 * @param array $map 配列。
	 * @phpstan-param non-empty-array<string,TTValue> $map
	 * @return self
	 * @phpstan-return self<TTValue>
	 */
	public static function create(array $map): self
	{
		if (ArrayUtility::isNullOrEmpty($map)) { //@phpstan-ignore-line ArrayUtility::isNullOrEmpty
			throw new ArgumentException('$map');
		}

		$firstKey = ArrayUtility::getFirstKey($map);
		$firstValue = $map[$firstKey];

		$type = TypeUtility::getType($firstValue);

		return new self($type, $map);
	}

	/**
	 * 空データの生成。
	 *
	 * @template TTValue
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @return self
	 * @phpstan-return self<TTValue>
	 */
	public static function empty(string $type): self //@phpstan-ignore-line TTValue
	{
		return new self($type, []);
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
		if (is_null($offset)) {
			throw new NotSupportedException();
		}

		$this->isValidType($value);

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
