<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotStringException;

/**
 * 文字列がバイトデータなのか普通の文字列なのかよくわからんのでこれでラップする。
 *
 * ソース上の型を明示するだけの目的で、効率とかは特になにもない。
 */
final class Bytes
{
	private string $value;

	/**
	 * 生成。
	 *
	 * @param string $value バイトデータとして扱う文字列。
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * バイトデータをそのまま取得。
	 *
	 * @return string
	 */
	public function getRaw(): string
	{
		return $this->value;
	}

	/**
	 * バイト長を取得。
	 *
	 * @return integer
	 */
	public function getLength(): int
	{
		return strlen($this->value);
	}

	/**
	 * 16進数文字列に変換。
	 *
	 * @return string [0-9a-z]{2} で構成された文字列。
	 */
	public function toHex(): string
	{
		return bin2hex($this->value);
	}

	/**
	 * base64 文字列に変換。
	 *
	 * @return string
	 */
	public function toBase64(): string
	{
		return base64_encode($this->value);
	}

	/**
	 * base64 文字列から Bytes を取得。
	 *
	 * @param string $base64
	 * @return Bytes
	 * @throws ArgumentException 変換失敗。
	 */
	public static function fromBase64(string $base64): Bytes
	{
		$value = base64_decode($base64, true);
		if ($value === false) {
			throw new ArgumentException('$base64');
		}

		return new Bytes($value);
	}

	public function toString(): string
	{
		$nullIndex = mb_strpos($this->value, "\0");
		if ($nullIndex !== false) {
			throw new NotStringException();
		}

		return $this->value;
	}
}
