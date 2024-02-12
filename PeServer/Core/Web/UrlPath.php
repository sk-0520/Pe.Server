<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\ArrayAccessHelper;
use PeServer\Core\Collection\Collections;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\TypeUtility;
use Stringable;
use TypeError;

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
	 * @var non-empty-string[]|null
	 */
	private array|null $elements;

	#endregion

	public function __construct(string $path)
	{
		if (Text::isNullOrWhiteSpace($path)) {
			$this->elements = null;
		} else {
			/** @phpstan-var non-empty-string[] */
			$elements = Collections::from(Text::split($path, '/'))
				->select(fn ($a) => Text::trim($a, '/'))
				->where(fn ($a) => !Text::isNullOrWhiteSpace($a))
				->toArray();

			foreach ($elements as $element) {
				if (!self::isValidElement($element)) {
					throw new ArgumentException($path);
				}
			}

			$this->elements = $elements;
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
	 * @phpstan-assert-if-true null $this->elements
	 * @phpstan-assert-if-false string[] $this->elements
	 */
	public function isEmpty(): bool
	{
		return $this->elements === null;
	}

	/**
	 * パスの各要素を取得。
	 *
	 * @return non-empty-string[]
	 */
	public function getElements(): array
	{
		if ($this->isEmpty()) {
			throw new InvalidOperationException('empty');
		}

		return $this->elements;
	}

	/**
	 * 終端パスを追加。
	 *
	 * @param string|string[] $element
	 * @phpstan-param string|non-empty-array<non-empty-string> $element
	 * @return self 終端パスの追加された `UrlPath`
	 */
	public function add(string|array $element): self
	{
		if (is_string($element)) {
			if (Text::isNullOrWhiteSpace($element)) {
				return $this;
			}
			$elements = [$element];
		} else {
			if (count($element) === 0) { //@phpstan-ignore-line [DOCTYPE]
				throw new ArgumentException('$element: empty array');
			}
			foreach ($element as $i => $elm) {
				if (is_string($elm)) { //@phpstan-ignore-line [DOCTYPE]
					if (Text::isNullOrWhiteSpace($elm)) { //@phpstan-ignore-line [DOCTYPE]
						throw new ArgumentException("\$element[$i]: whitespace string");
					}
				} else {
					throw new ArgumentException("\$element[$i]: not string");
				}
			}
			$elements = $element;
		}

		if ($this->isEmpty()) {
			return self::from($elements);
		} else {
			return self::from([...$this->elements, ...$elements]);
		}
	}

	public function toString(bool $trailingSlash): string
	{
		if ($this->isEmpty()) {
			return Text::EMPTY;
		}

		if (!Arr::getCount($this->elements)) {
			return '/';
		}

		return '/' . Text::join('/', $this->elements) . ($trailingSlash ? '/' : Text::EMPTY);
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
		if (!ArrayAccessHelper::offsetExistsUInt($offset)) { //@phpstan-ignore-line [DOCTYPE] UnsignedIntegerAlias
			return false;
		}

		if ($this->isEmpty()) {
			return false;
		}

		return isset($this->elements[$offset]);
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
		ArrayAccessHelper::offsetGetUInt($offset); //@phpstan-ignore-line [DOCTYPE] UnsignedIntegerAlias

		if ($this->isEmpty()) {
			throw new IndexOutOfRangeException((string)$offset);
		}

		if (!isset($this->elements[$offset])) {
			throw new IndexOutOfRangeException((string)$offset);
		}

		return $this->elements[$offset];
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

		return count($this->elements);
	}

	#endregion

	#region IteratorAggregate

	public function getIterator(): Iterator
	{
		return new ArrayIterator($this->elements ?? []);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString(false);
	}

	#endregion
}
