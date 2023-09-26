<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use Stringable;
use TypeError;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\TypeUtility;

/**
 * URL のパス構成要素。
 * @implements ArrayAccess<UnsignedIntegerAlias,string>
 * @implements IteratorAggregate<UnsignedIntegerAlias,string>
 */
readonly class UrlPath implements ArrayAccess, Countable, IteratorAggregate, Stringable
{
	#region variable

	/**
	 * 構成要素
	 *
	 * `null` の場合はほんとになんもない(ホストの後の `/` もない)
	 * 配列要素数が 0 の場合は `/` のみ
	 *
	 * @var string[]|null
	 */
	private array|null $pathElements;

	#endregion

	public function __construct(string $path)
	{
		if (Text::isNullOrWhiteSpace($path)) {
			$this->pathElements = null;
		} else {
			$elements = Collection::from(Text::split($path, '/'))
				->select(fn ($a) => Text::trim($a, '/'))
				->where(fn ($a) => !Text::isNullOrWhiteSpace($a))
				->toArray();

			foreach ($elements as $element) {
				if (!self::isValidElement($element)) {
					throw new ArgumentException($path);
				}
			}

			$this->pathElements = $elements;
		}
	}

	#region function

	/**
	 * パスの各要素から生成。
	 *
	 * @param string[] $elements
	 * @return self
	 */
	public static function from(array $elements): self
	{
		return new self(Text::join('/', $elements));
	}

	public static function isValidElement(string $element): bool
	{
		$invalids = ['/', '?', '#'];

		foreach ($invalids as $invalid) {
			if (Text::contains($element, $invalid, false)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * ルートの `/` すら持たない空のパスか。
	 *
	 * @return bool
	 * @phpstan-assert-if-true null $this->pathElements
	 * @phpstan-assert-if-false string[] $this->pathElements
	 */
	public function isEmpty(): bool
	{
		return $this->pathElements === null;
	}

	/**
	 * パスの各要素を取得。
	 *
	 * @return string[]
	 */
	public function getElements(): array
	{
		if ($this->isEmpty()) {
			throw new InvalidOperationException('empty');
		}

		return $this->pathElements;
	}

	/**
	 * 終端パスを追加。
	 *
	 * @param string $element
	 * @return self 終端パスの追加された `UrlPath`
	 */
	public function add(string $element): self
	{
		if (Text::isNullOrWhiteSpace($element)) {
			return $this;
		}

		if ($this->isEmpty()) {
			return new self($element);
		} else {
			return self::from([...$this->pathElements, $element]);
		}
	}

	public function toString(bool $addLastSeparator): string
	{
		if ($this->isEmpty()) {
			return Text::EMPTY;
		}

		if (!Arr::getCount($this->pathElements)) {
			return '/';
		}

		return '/' . Text::join('/', $this->pathElements) . ($addLastSeparator ? '/' : Text::EMPTY);
	}

	#endregion

	#region ArrayAccess

	/**
	 * @param int $offset
	 * @phpstan-param UnsignedIntegerAlias $offset
	 * @return bool
	 * @see ArrayAccess::offsetExists
	 */
	public function offsetExists(mixed $offset): bool
	{
		if (!is_int($offset)) { //@phpstan-ignore-line [DOCTYPE] UnsignedIntegerAlias
			return false;
		}

		if ($offset < 0) { //@phpstan-ignore-line [DOCTYPE] UnsignedIntegerAlias
			return false;
		}

		if ($this->isEmpty()) {
			return false;
		}

		if (count($this->pathElements) <= $offset) {
			return false;
		}

		return true;
	}

	/**
	 * @param int $offset
	 * @phpstan-param UnsignedIntegerAlias $offset
	 * @return string
	 * @throws TypeError
	 * @throws IndexOutOfRangeException
	 * @see ArrayAccess::offsetGet
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (!is_int($offset)) { //@phpstan-ignore-line UnsignedIntegerAlias
			throw new TypeError(TypeUtility::getType($offset));
		}

		if ($offset < 0) { //@phpstan-ignore-line UnsignedIntegerAlias
			throw new IndexOutOfRangeException((string)$offset);
		}

		if ($this->isEmpty()) {
			throw new IndexOutOfRangeException((string)$offset);
		}

		if (count($this->pathElements) <= $offset) {
			throw new IndexOutOfRangeException((string)$offset);
		}

		return $this->pathElements[$offset];
	}

	/** @throws NotSupportedException */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new NotSupportedException();
	}

	/** @throws NotSupportedException */
	public function offsetUnset(mixed $offset): void
	{
		throw new NotSupportedException();
	}

	#endregion

	#region Countable

	/**
	 * Countable::count
	 *
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function count(): int
	{
		if ($this->isEmpty()) {
			return 0;
		}

		return count($this->pathElements);
	}

	#endregion

	#region IteratorAggregate

	public function getIterator(): Iterator
	{
		return new ArrayIterator($this->pathElements ?? []);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString(false);
	}

	#endregion
}
