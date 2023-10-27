<?php

declare(strict_types=1);

namespace PeServer\Core;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use Stringable;
use TypeError;
use PeServer\Core\Collections\ArrayAccessHelper;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\NullByteStringException;
use PeServer\Core\Throws\BinaryException;
use PeServer\Core\Throws\Throws;
use ValueError;

/**
 * PHP文字列がバイトデータなのか普通の文字列なのかよくわからん。
 *
 * ソース上の型を明示するだけの目的で、効率とかは特になにもない。
 * あとUTF8で動くこと前提。
 *
 * @phpstan-type Byte int<0,255>
 * @implements ArrayAccess<UnsignedIntegerAlias,Byte>
 * @implements IteratorAggregate<UnsignedIntegerAlias,Byte>
 */
readonly final class Binary implements ArrayAccess, IteratorAggregate, Countable, Stringable
{
	/**
	 * 生成。
	 *
	 * @param string $raw バイトデータとして扱う文字列。
	 */
	public function __construct(
		public readonly string $raw
	) {
		//NOP
	}

	#region function

	/**
	 * `substr` ラッパー。
	 *
	 * @param int $index
	 * @param int|null $length
	 * @return self
	 */
	public function getRange(int $index, ?int $length = null): self
	{
		$raw = substr($this->raw, $index, $length);
		return new self($raw);
	}

	/**
	 * 指定したバイナリと等しいか。
	 *
	 * @param self $target 対象バイナリ。
	 * @return bool 等しいか。
	 */
	public function isEquals(self $target): bool
	{
		return $this->raw === $target->raw;
	}

	/**
	 * 16進数文字列に変換。
	 *
	 * `bin2hex` ラッパー。
	 *
	 * @return string `[0-9a-f]{2}*` で構成された文字列。
	 * @see https://www.php.net/manual/function.bin2hex.php
	 */
	public function toHex(): string
	{
		return bin2hex($this->raw);
	}

	// public function convert(int $from, int $to): string
	// {
	// 	return base_convert($this->raw, $from, $to);
	// }

	/**
	 * base64 文字列に変換。
	 *
	 * `base64_encode` ラッパー。
	 *
	 * @return string
	 * @see https://www.php.net/manual/function.base64-encode.php
	 */
	public function toBase64(): string
	{
		return base64_encode($this->raw);
	}

	/**
	 * base64 文字列から Binary を取得。
	 *
	 * `base64_decode` ラッパー。
	 *
	 * @param string $base64
	 * @return Binary
	 * @throws ArgumentException 変換失敗。
	 * @see https://www.php.net/manual/function.base64-decode.php
	 */
	public static function fromBase64(string $base64): Binary
	{
		$value = base64_decode($base64, true);
		if ($value === false) {
			throw new ArgumentException('$base64');
		}

		return new Binary($value);
	}

	/**
	 * NULLバイトを持つか。
	 *
	 * @return boolean NULLバイトを持つ。
	 */
	public function hasNull(): bool
	{
		$nullIndex = mb_strpos($this->raw, "\0");
		return $nullIndex !== false;
	}

	/**
	 * バイトデータを文字列に変換。
	 *
	 * @return string
	 * @throws NullByteStringException NULLバイトが存在する。
	 */
	public function toString(): string
	{
		if ($this->hasNull()) {
			throw new NullByteStringException();
		}

		return $this->raw;
	}

	/**
	 * 配列化。
	 *
	 * `unpack` ラッパー。
	 *
	 * @param string $format
	 * @param int $offset
	 * @phpstan-param UnsignedIntegerAlias $offset
	 * @return array<mixed>
	 * @see https://www.php.net/manual/function.unpack.php
	 */
	public function toArray(string $format, int $offset = 0): array
	{
		$result = Throws::wrap(ValueError::class, BinaryException::class, fn () => unpack($format, $this->raw, $offset));
		if ($result === false) {
			throw new BinaryException();
		}

		/** @var array<mixed> */
		return $result;
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

		if (strlen($this->raw) <= $offset) {
			return false;
		}

		return true;
	}

	/**
	 * @param int $offset
	 * @phpstan-param UnsignedIntegerAlias $offset
	 * @return int
	 * @phpstan-return Byte
	 * @throws TypeError
	 * @throws IndexOutOfRangeException
	 * @see ArrayAccess::offsetGet
	 */
	public function offsetGet(mixed $offset): mixed
	{
		ArrayAccessHelper::offsetGetUInt($offset); //@phpstan-ignore-line [DOCTYPE] UnsignedIntegerAlias

		if (strlen($this->raw) <= $offset) {
			throw new IndexOutOfRangeException((string)$offset);
		}

		/** @phpstan-var Byte */
		return ord($this->raw[$offset]);
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

	#region IteratorAggregate

	public function getIterator(): Iterator
	{
		return new ArrayIterator(str_split($this->raw));
	}

	#endregion

	#region Countable

	/**
	 * バイト長を取得。
	 *
	 * `strlen` ラッパー。
	 *
	 * @return integer
	 * @phpstan-return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.strlen.php
	 */
	public function count(): int
	{
		return strlen($this->raw);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		if ($this->hasNull()) {
			return $this->toHex();
		}

		return $this->raw;
	}

	#endregion
}
