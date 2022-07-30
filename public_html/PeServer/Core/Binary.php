<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Stringable;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NullByteStringException;

/**
 * PHP文字列がバイトデータなのか普通の文字列なのかよくわからん。
 *
 * ソース上の型を明示するだけの目的で、効率とかは特になにもない。
 * あとUTF8で動くこと前提。
 */
final class Binary implements Stringable
{
	/**
	 * 実体。
	 *
	 * @var string
	 * @readonly
	 */
	private string $binary;

	/**
	 * 生成。
	 *
	 * @param string $binary バイトデータとして扱う文字列。
	 */
	public function __construct(string $binary)
	{
		$this->binary = $binary;
	}

	/**
	 * バイトデータをそのまま取得。
	 *
	 * @return string
	 */
	public function getRaw(): string
	{
		return $this->binary;
	}

	/**
	 * バイト長を取得。
	 *
	 * `strlen` ラッパー。
	 *
	 * @return integer
	 * @phpstan-return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.strlen.php
	 */
	public function getLength(): int
	{
		return strlen($this->binary);
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
		return bin2hex($this->binary);
	}

	// public function convert(int $from, int $to): string
	// {
	// 	return base_convert($this->binary, $from, $to);
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
		return base64_encode($this->binary);
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
		$nullIndex = mb_strpos($this->binary, "\0");
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

		return $this->binary;
	}

	public function __toString(): string
	{
		if ($this->hasNull()) {
			return $this->toHex();
		}

		return $this->binary;
	}

	// public function format(string $format, int $offset = 0): array
	// {
	// 	$result = unpack($format, $this->binary, $offset);
	// 	return $result;
	// }


}
