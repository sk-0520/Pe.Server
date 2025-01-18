<?php

declare(strict_types=1);

namespace PeServer\Core\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use PeServer\Core\Text;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\NotSupportedException;

/**
 * キーとして大文字小文字を区別しない連想配列。
 *
 * * 追加という概念はない(`$array[] = 'xxx'`)
 * * 数値も受け取れるけど仕方ないとして割り切る
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayAccess<TKey,TValue>
 * @implements IteratorAggregate<TKey,TValue>
 */
class CaseInsensitiveKeyArray implements ArrayAccess, Countable, IteratorAggregate
{
	#region variable

	/**
	 * 実データ。
	 *
	 * @var array<string|int,mixed>
	 * @phpstan-var array<array-key,TValue>
	 */
	private array $data = [];
	/**
	 * self::toMapKey適用キーに対する実キーをマッピング。
	 *
	 * @var array<string,string>
	 */
	private array $map = [];

	#endregion

	/**
	 * 生成。
	 *
	 * @param array<string,string|int>|null $input
	 * @phpstan-param array<array-key,TValue>|null $input
	 */
	public function __construct(array $input = null)
	{
		if ($input !== null) {
			foreach ($input as $key => $value) {
				$this->offsetSet($key, $value);
			}
		}
	}

	#region function

	/**
	 * オフセット名へのマッピング名に変換。
	 *
	 * @param string $offset
	 * @return string
	 * @phpstan-pure
	 */
	public function toMapKey(string $offset): string
	{
		return Text::toLower($offset);
	}

	#endregion

	#region ArrayAccess

	/**
	 * `ArrayAccess:offsetExists`
	 *
	 * @param string|int $offset
	 * @return bool
	 * @phpstan-pure
	 */
	public function offsetExists(mixed $offset): bool
	{
		if (isset($this->data[$offset])) {
			return true;
		}
		if (is_int($offset)) {
			return false;
		}

		$mapOffset = $this->toMapKey($offset);
		if (isset($this->map[$mapOffset])) {
			return true;
		}

		return false;
	}

	/**
	 * `ArrayAccess:offsetGet`
	 *
	 * @param string|int $offset
	 * @return mixed
	 * @phpstan-return TValue
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (isset($this->data[$offset])) {
			return $this->data[$offset];
		}
		if (is_int($offset)) {
			throw new IndexOutOfRangeException('$offset: ' . $offset);
		}

		$mapOffset = $this->toMapKey($offset);
		if (isset($this->map[$mapOffset])) {
			return $this->data[$this->map[$mapOffset]];
		}

		throw new KeyNotFoundException('$offset: ' . $offset);
	}

	/**
	 * `ArrayAccess:offsetSet`
	 *
	 * @param string|int|null $offset
	 * @param mixed $value
	 * @phpstan-param TValue $value
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			throw new NotSupportedException();
		}
		if (is_int($offset)) {
			$this->data[$offset] = $value;
			return;
		}
		if (is_double($offset)) { //@phpstan-ignore-line サポートしているがドキュメント上は通常配列キー限定
			$floatOffset = (string)$offset;
			$this->data[$floatOffset] = $value;
			return;
		}

		$mapOffset = $this->toMapKey($offset);
		if (isset($this->map[$mapOffset])) {
			$this->data[$this->map[$mapOffset]] = $value;
		} else {
			$this->data[$offset] = $value;
			$this->map[$mapOffset] = $offset;
		}
	}

	/**
	 * `ArrayAccess:offsetUnset`
	 *
	 * @param string|int $offset
	 */
	public function offsetUnset(mixed $offset): void
	{
		if (is_int($offset)) {
			unset($this->data[$offset]);
			return;
		}
		if (is_double($offset)) { //@phpstan-ignore-line サポートしているがドキュメント上は通常配列キー限定
			unset($this->data[(string)$offset]);
			return;
		}

		$mapOffset = $this->toMapKey($offset);
		unset($this->data[$this->map[$mapOffset]]);
		unset($this->map[$mapOffset]);
	}

	#endregion

	#region Countable

	public function count(): int
	{
		return count($this->data);
	}

	#endregion

	#region IteratorAggregate

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->data);
	}

	#endregion
}
