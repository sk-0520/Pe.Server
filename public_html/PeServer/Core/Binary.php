<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotStringException;

/**
 * 文字列がバイトデータなのか普通の文字列なのかよくわからんのでこれでラップする。
 *
 * ソース上の型を明示するだけの目的で、効率とかは特になにもない。
 * あとUTF8で動くこと前提。
 */
final class Binary
{
	/**
	 * 実体。
	 *
	 * @var string
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
	 * @return integer
	 */
	public function getLength(): int
	{
		return strlen($this->binary);
	}

	/**
	 * 16進数文字列に変換。
	 *
	 * @return string [0-9a-f]{2} で構成された文字列。
	 */
	public function toHex(): string
	{
		return bin2hex($this->binary);
	}

	public function convert(int $from, int $to): string
	{
		return base_convert($this->binary, $from, $to);
	}

	/**
	 * base64 文字列に変換。
	 *
	 * @return string
	 */
	public function toBase64(): string
	{
		return base64_encode($this->binary);
	}

	/**
	 * base64 文字列から Binary を取得。
	 *
	 * @param string $base64
	 * @return Binary
	 * @throws ArgumentException 変換失敗。
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
	 * @throws NotStringException NULLバイトが存在する。
	 */
	public function toString(): string
	{
		if ($this->hasNull()) {
			throw new NotStringException();
		}

		return $this->binary;
	}
}
