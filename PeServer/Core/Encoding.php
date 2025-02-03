<?php

declare(strict_types=1);

namespace PeServer\Core;

use ValueError;
use PeServer\Core\Binary;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\EncodingException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\Throws;
use Throwable;

/**
 * エンコーディング処理。
 *
 * コンストラクタで指定されたエンコード名に対して通常文字列へあれこれする。
 */
class Encoding
{
	#region define

	/** アスキー */
	public const ENCODE_ASCII = 'ASCII';

	/** UTF-7 */
	public const ENCODE_UTF7 = 'UTF-7';
	/** UTF-8 */
	public const ENCODE_UTF8 = 'UTF-8';
	/** UTF-16 */
	public const ENCODE_UTF16_PLAIN = 'UTF-16';
	public const ENCODE_UTF16_BE = 'UTF-16BE';
	public const ENCODE_UTF16_LE = 'UTF-16LE';
	public const ENCODE_UTF16_DEFAULT = self::ENCODE_UTF16_LE;
	/** UTF-32 */
	public const ENCODE_UTF32_PLAIN = 'UTF-32';
	public const ENCODE_UTF32_BE = 'UTF-32BE';
	public const ENCODE_UTF32_LE = 'UTF-32LE';
	public const ENCODE_UTF32_DEFAULT = self::ENCODE_UTF32_LE;

	/** SJIS(SHIFT-JIS) */
	public const ENCODE_SJIS_PLAIN = 'SJIS';
	/** SJIS(CP932) */
	public const ENCODE_SJIS_WIN31J = 'CP932';
	/** SJIS(Windows) */
	public const ENCODE_SJIS_WIN = 'SJIS-win';
	/** SJIS(Shift_JIS-2004) */
	public const ENCODE_SJIS_2004 = 'SJIS-2004';
	/** SJIS(何も考えず使う用) */
	public const ENCODE_SJIS_DEFAULT = self::ENCODE_SJIS_WIN31J;

	public const ENCODE_JIS_PLAIN = 'JIS';
	public const ENCODE_JIS_DEFAULT = self::ENCODE_JIS_PLAIN;

	public const ENCODE_EUC_JP_PLAIN = 'EUC-JP';
	public const ENCODE_EUC_JP_WIN = 'eucJP-win';
	public const ENCODE_EUC_JP_DEFAULT = self::ENCODE_EUC_JP_WIN;

	#endregion

	#region variable

	/**
	 * キャッシュされたエンコーディング名一覧。
	 *
	 * @var string[]|null
	 */
	protected static ?array $cacheNames = null;

	/**
	 * デフォルトエンコーディング。
	 *
	 * `setDefaultEncoding` で設定され、
	 * `getDefaultEncoding` で使用される。
	 *
	 * ただし `getDefaultEncoding` で本プロパティ未設定の場合は上書きされる。
	 *
	 * @var self|null
	 */
	private static ?self $defaultEncoding = null;

	/**
	 * エンコード名。
	 */
	public readonly string $name;

	#endregion

	/**
	 * 生成
	 *
	 * @param string $name エンコード名。
	 * @phpstan-param non-empty-string|Encoding::ENCODE_* $name
	 */
	public function __construct(string $name)
	{
		self::throwIfInvalidEncodingName($name);
		$this->name = $name;
	}

	#region function

	/**
	 * エンコーディング名が正しいか。
	 *
	 * @param string $name
	 * @throws ArgumentException 正しくない。
	 */
	private static function throwIfInvalidEncodingName(string $name): void
	{
		$names = self::getEncodingNames();
		if (!Arr::containsValue($names, $name)) {
			throw new ArgumentException('$name');
		}
	}

	/**
	 * エンコーディング名一覧を取得。
	 *
	 * キャッシュされる。
	 *
	 * `mb_list_encodings` ラッパー。
	 *
	 * @return string[]
	 * @see https://www.php.net/manual/function.mb-list-encodings.php
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
		if (self::$defaultEncoding === null) {
			$name = mb_internal_encoding();
			if (Text::isNullOrEmpty($name)) {
				throw new InvalidOperationException();
			}

			return self::$defaultEncoding = new self($name);
		}

		return self::$defaultEncoding;
	}

	/**
	 * `mb_internal_encoding` ラッパー
	 *
	 * @param Encoding $encoding
	 * @throws ArgumentException
	 * @see https://www.php.net/manual/function.mb-internal-encoding.php
	 */
	public static function setDefaultEncoding(Encoding $encoding): void
	{
		$result = mb_internal_encoding($encoding->name);
		if (!$result) {
			throw new ArgumentException('$encoding');
		}

		self::$defaultEncoding = $encoding;
	}

	/**
	 * ASCIIエンコーディング。
	 *
	 * @return Encoding
	 */
	public static function getAscii(): Encoding
	{
		return new self(self::ENCODE_ASCII);
	}

	/**
	 * UTF8エンコーディング。
	 *
	 * @return Encoding
	 */
	public static function getUtf8(): Encoding
	{
		return new self(self::ENCODE_UTF8);
	}

	/**
	 * UTF16エンコーディング。
	 *
	 * @return Encoding
	 */
	public static function getUtf16(): Encoding
	{
		return new self(self::ENCODE_UTF16_DEFAULT);
	}

	/**
	 * UTF32エンコーディング。
	 *
	 * @return Encoding
	 */
	public static function getUtf32(): Encoding
	{
		return new self(self::ENCODE_UTF32_DEFAULT);
	}

	/**
	 * SJISエンコーディング。
	 *
	 * @return Encoding
	 */
	public static function getShiftJis(): Encoding
	{
		return new self(self::ENCODE_SJIS_DEFAULT);
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
			return new Binary($output);
		} catch (Throwable $ex) {
			Throws::reThrow(EncodingException::class, $ex);
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
			return $input->raw;
		}

		try {
			$output = mb_convert_encoding($input->raw, $default->name, $this->name);
			return $output;
		} catch (Throwable $ex) {
			Throws::reThrow(EncodingException::class, $ex);
		}
	}

	/**
	 * 現在のエンコーディング・タイプのエイリアスを取得。
	 *
	 * @return string[]
	 * @see https://www.php.net/manual/function.mb-encoding-aliases.php
	 */
	public static function getAliasNames(string $encoding): array
	{
		try {
			$names = mb_encoding_aliases($encoding);
			return $names;
		} catch (Throwable $ex) {
			Throws::reThrow(EncodingException::class, $ex);
		}
	}

	/**
	 * 現在のエンコーディングで有効か。
	 *
	 * @param Binary $input
	 * @return bool 有効か。
	 */
	public function isValid(Binary $input): bool
	{
		return mb_check_encoding($input->raw, $this->name);
	}

	/**
	 * 現在のエンコーディングからBOMを取得する。
	 *
	 * @return Binary エンコーディング対するBOM。対応しない場合空のバイナリ。
	 */
	public function getByteOrderMark(): Binary
	{
		//wikipedia の bom 見たけどわからん。
		//TODO: 定義しているエンコーディングともあわんし、どうしたもんか
		$bomMap = [
			'UTF-8' => "\xEF\xBB\xBF",
			'UTF-16BE' => "\xFE\xFF",
			'UTF-16LE' => "\xFF\xFE",
			'UTF-32BE' => "\x00\x00\xFE\xFF",
			'UTF-32LE' => "\xFF\xFE\x00\x00",
		];

		if (isset($bomMap[$this->name])) {
			return new Binary($bomMap[$this->name]);
		}

		return new Binary('');
	}

	#endregion
}
