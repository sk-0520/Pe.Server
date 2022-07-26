<?php

declare(strict_types=1);

namespace PeServer\Core;

use \ValueError;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\EncodingException;
use PeServer\Core\Throws\Throws;

class Encoding
{
	public const ENCODE_ASCII = 'ASCII';
	public const ENCODE_UTF8 = 'UTF-8';
	public const ENCODE_UTF16 = 'UTF-16';
	public const ENCODE_UTF32 = 'UTF-32';
	public const ENCODE_SHIFT_JIS_WIN = 'SJIS-win';

	/**
	 * キャッシュされたエンコーディング名一覧。
	 *
	 * @var string[]|null
	 */
	protected static ?array $cacheNames = null;

	/**
	 * エンコード名。
	 *
	 * @readonly
	 */
	public string $name;

	/**
	 * 生成
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string|Encoding::ENCODE_* $name
	 */
	public function __construct(string $name)
	{
		self::enforceEncodingName($name);
		$this->name = $name;
	}

	/**
	 * エンコーディング名が正しいか。
	 *
	 * @param string $name
	 * @throws ArgumentException 正しくない。
	 */
	private static function enforceEncodingName(string $name): void
	{
		$names = self::getEncodingNames();
		if (!ArrayUtility::containsValue($names, $name)) {
			throw new ArgumentException('$name');
		}
	}

	/**
	 * エンコーディング名一覧を取得。
	 *
	 * キャッシュされる。
	 *
	 * @return string[]
	 */
	public static function getEncodingNames(): array
	{
		return self::$cacheNames ??= mb_list_encodings();
	}

	/**
	 * `mb_internal_encoding` ラッパー
	 *
	 * @return Encoding
	 * @see https://www.php.net/manual/function.mb-internal-encoding.php
	 */
	public static function getDefaultEncoding(): Encoding
	{
		$name = mb_internal_encoding();
		//@phpstan-ignore-next-line
		return new Encoding($name);
	}

	/**
	 * `mb_internal_encoding` ラッパー
	 *
	 * @param Encoding $encoding
	 * @see https://www.php.net/manual/function.mb-internal-encoding.php
	 */
	public static function setDefaultEncoding(Encoding $encoding): void
	{
		$result = mb_internal_encoding($encoding->name);
		if (!$result) {
			throw new ArgumentException('$encoding');
		}
	}

	/**
	 * UTF8エンコーディング。
	 *
	 * @return Encoding
	 */
	public static function getUtf8Encoding(): Encoding
	{
		return new Encoding('UTF-8');
	}

	/**
	 * 文字列(デフォルトエンコーディング)を現在のエンコーディングへ変換。
	 *
	 * @param string $input
	 * @return Binary
	 * @throws EncodingException
	 * @see https://www.php.net/manual/function.mb-convert-encoding.php
	 */
	public function getBinary(string $input): Binary
	{
		$default = self::getDefaultEncoding();
		if ($default->name === $this->name) {
			return new Binary($input);
		}

		try {
			$output = mb_convert_encoding($input, $this->name, $default->name);
			if ($output === false) { //@phpstan-ignore-line
				throw new EncodingException();
			}
			return new Binary($output);
		} catch (ValueError $err) {
			Throws::reThrow(EncodingException::class, $err);
		}
	}

	/**
	 * 現在のエンコーディングデータを文字列(デフォルトエンコーディング)へ変換。
	 *
	 * @param Binary $input
	 * @return string
	 * @throws EncodingException
	 * @see https://www.php.net/manual/function.mb-convert-encoding.php
	 */
	public function toString(Binary $input): string
	{
		$default = self::getDefaultEncoding();
		if ($default->name === $this->name) {
			return $input->getRaw();
		}

		try {
			$output = mb_convert_encoding($input->getRaw(), $default->name, $this->name);
			if ($output === false) { //@phpstan-ignore-line
				throw new EncodingException();
			}
			return $output;
		} catch (ValueError $err) {
			Throws::reThrow(EncodingException::class, $err);
		}
	}

	/**
	 * 現在のエンコーディング・タイプのエイリアスを取得。
	 *
	 * @return string[]
	 * @see https://www.php.net/manual/function.mb-encoding-aliases.php
	 */
	public function getAliasNames(): array
	{
		$names = mb_encoding_aliases($this->name);
		if ($names === false) { //@phpstan-ignore-line
			throw new EncodingException($this->name);
		}

		return $names;
	}

	/**
	 * 現在のエンコーディングで有効か。
	 *
	 * @param Binary $input
	 * @return bool 有効か。
	 */
	public function isValid(Binary $input): bool
	{
		return mb_check_encoding($input->getRaw(), $this->name);
	}
}
