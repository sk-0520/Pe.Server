<?php

declare(strict_types=1);

namespace PeServer\Core;

use Exception;
use Throwable;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\Throws;

/**
 * 暗号化周り
 */
abstract class Cryptography
{
	#region define

	private const OPTION = 0;
	/** 文字列への暗号化時のセパレータ */
	public const SEPARATOR = '@';
	/** 文字列乱数 標準文字列 */
	public const DEFAULT_RANDOM_STRING = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	/** 文字列乱数 ファイルとして扱える文字列 */
	public const FILE_RANDOM_STRING = '0123456789abcdefghijklmnopqrstuvwxyz';

	#endregion

	#region function

	/**
	 * 乱数取得。
	 *
	 * `random_int` ラッパー。
	 *
	 * @param integer $max 最大値。
	 * @param integer $min 最小値。
	 * @return integer 乱数。
	 * @throws CryptoException 失敗
	 * @see https://www.php.net/manual/function.random-int.php
	 */
	public static function generateRandomInteger(int $max = PHP_INT_MAX, int $min = 0): int
	{
		try {
			return random_int($min, $max);
		} catch (Throwable $ex) {
			Throws::reThrow(CryptoException::class, $ex);
		}
	}

	/**
	 * ランダムバイナリデータを生成。
	 *
	 * `openssl_random_pseudo_bytes` ラッパー。
	 *
	 * @param integer $length バイト数。
	 * @phpstan-param positive-int $length
	 * @return Binary バイナリデータ。
	 * @throws CryptoException 失敗
	 * @see https://www.php.net/manual/function.openssl-random-pseudo-bytes.php
	 */
	public static function generateRandomBinary(int $length): Binary
	{
		if ($length < 1) { //@phpstan-ignore-line [PHPDOC]
			throw new CryptoException('$length: ' . $length);
		}

		$result = openssl_random_pseudo_bytes($length);
		if ($result === false) { //@phpstan-ignore-line [PHPVERSION]
			throw new CryptoException();
		}

		return new Binary($result);
	}

	/**
	 * ランダム文字列を生成。
	 *
	 * @param integer $length 文字列長。
	 * @phpstan-param positive-int $length
	 * @param string $characters ランダム文字の元になる文字列。
	 * @phpstan-param non-empty-string $characters
	 * @return string 文字列。
	 * @throws CryptoException 失敗
	 */
	public static function generateRandomString(int $length, string $characters = self::DEFAULT_RANDOM_STRING): string
	{
		if ($length < 1) { //@phpstan-ignore-line [PHPDOC]
			throw new ArgumentException('$length: ' . $length);
		}
		if (Text::isNullOrWhiteSpace($characters)) { //@phpstan-ignore-line [PHPDOC]
			throw new ArgumentException('$characters: ' . $characters);
		}

		$charactersArray = Text::toCharacters($characters);

		$min = 0;
		$max = Arr::getCount($charactersArray) - 1;

		$result = '';

		for ($i = 0; $i < $length; $i++) {
			$index = self::generateRandomInteger($max, $min);
			$result .= $charactersArray[$index];
		}

		return $result;
	}

	/**
	 * 文字列を暗号化。
	 *
	 * @param string $algorithm 暗号化方法。
	 * @phpstan-param non-empty-string $algorithm
	 * @param string $rawValue 生文字列。
	 * @param string $password パスワード。
	 * @return string 暗号化された文字列。 アルゴリズム@IV@暗号化データ となる。
	 * @throws CryptoException 失敗
	 */
	public static function encrypt(string $algorithm, string $rawValue, string $password): string
	{
		$ivLength = 0;
		try {
			$ivLength = openssl_cipher_iv_length($algorithm);
		} catch (Exception $ex) {
			Throws::reThrow(CryptoException::class, $ex, $algorithm);
		}

		if ($ivLength === false) {
			throw new CryptoException($algorithm);
		}
		if ($ivLength < 1) {
			throw new CryptoException('$ivLength: ' . $ivLength);
		}

		$iv = self::generateRandomBinary($ivLength);

		$encData = openssl_encrypt($rawValue, $algorithm, $password, self::OPTION, $iv->raw);
		if ($encData === false) {
			throw new CryptoException();
		}

		return $algorithm . self::SEPARATOR . $iv->toBase64() . self::SEPARATOR . $encData;
	}

	/**
	 * Cryptography::encrypt で暗号化されたデータの復元。
	 *
	 * @param string $encValue 暗号化データ。
	 * @param string $password パスワード。
	 * @return string 生文字列。
	 * @throws CryptoException 失敗
	 */
	public static function decrypt(string $encValue, string $password): string
	{
		$values = Text::split($encValue, self::SEPARATOR);
		if (Arr::getCount($values) !== 3) {
			throw new ArgumentException();
		}
		list($algorithm, $ivBase64, $encData) = $values;

		$iv = Binary::fromBase64($ivBase64);

		/** @var string|false */
		$decData = false;
		try {
			$decData = openssl_decrypt($encData, $algorithm, $password, self::OPTION, $iv->raw);
		} catch (Exception $ex) {
			Throws::reThrow(CryptoException::class, $ex, $algorithm);
		}

		if ($decData === false) {
			throw new CryptoException();
		}

		return $decData;
	}

	/**
	 * `password_hash($password, PASSWORD_DEFAULT)`ラッパー。
	 *
	 * @param string $password 生パスワード。
	 * @return string ハッシュ化パスワード。
	 * @see https://www.php.net/manual/function.password-hash.php
	 */
	public static function hashPassword(string $password): string
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * `password_verify(string $password, $hashPassword)` ラッパー。
	 *
	 * @param string $password 生パスワード。
	 * @param string $hashPassword ハッシュ化パスワード。
	 * @return boolean 一致。
	 * @see https://www.php.net/manual/function.password-verify.php
	 */
	public static function verifyPassword(string $password, string $hashPassword): bool
	{
		return password_verify($password, $hashPassword);
	}

	/**
	 * `password_needs_rehash` ラッパー。
	 *
	 * @param string $hashPassword
	 * @return bool
	 * @see https://www.php.net/manual/function.password-needs-rehash.php
	 */
	public static function needPasswordReset(string $hashPassword): bool
	{
		return password_needs_rehash($hashPassword, null, []);
	}

	/**
	 * `password_get_info` ラッパー。
	 *
	 * @param string $hashPassword
	 * @return array{algo:string,algoName:string,options:array<mixed>}
	 * @see https://www.php.net/manual/function.password-get-info.php
	 */
	public static function getPasswordInformation(string $hashPassword): array
	{
		//@phpstan-ignore-next-line [PHPDOC]
		return password_get_info($hashPassword);
	}

	/**
	 * `password_algos` ラッパー。
	 *
	 * @return string[]
	 * @see https://www.php.net/manual/function.password-algos.php
	 */
	public static function getPasswordAlgorithms(): array
	{
		return password_algos();
	}

	/**
	 * ハッシュアルゴリズム一覧。
	 *
	 * `hash_algos` ラッパー。
	 *
	 * @return string[]
	 * @see https://www.php.net/manual/function.hash-algos.php
	 */
	public static function getHashAlgorithms(): array
	{
		return hash_algos();
	}

	/**
	 * ハッシュ値生成。
	 *
	 * @param bool $isBinary
	 * @param string $algorithm
	 * @param Binary $binary
	 * @param array{seed?:?int}|null $options
	 * @return string
	 */
	private static function generateHashCore(bool $isBinary, string $algorithm, Binary $binary, ?array $options): string
	{
		//$hash = hash($algorithm, $binary->raw, $isBinary, $options);
		$hash = hash($algorithm, $binary->raw, $isBinary);
		if ($hash === false) { //@phpstan-ignore-line [PHP_VERSION]
			throw new CryptoException();
		}

		return $hash;
	}

	/**
	 * ハッシュ化処理(文字列)。
	 *
	 * `hash` ラッパー。
	 *
	 * @param string $algorithm
	 * @phpstan-param non-empty-string $algorithm
	 * @param Binary $binary 入力バイナリデータ。
	 * @param array{seed?:?int}|null $options
	 * @return string 文字列表現。
	 * @throws CryptoException
	 * @see https://www.php.net/manual/function.hash.php
	 */
	public static function generateHashString(string $algorithm, Binary $binary, ?array $options = null): string
	{
		return self::generateHashCore(false, $algorithm, $binary, $options);
	}

	/**
	 * ハッシュ化処理(バイナリ)。
	 *
	 * `hash` ラッパー。
	 *
	 * @param string $algorithm アルゴリズム。
	 * @phpstan-param non-empty-string $algorithm
	 * @param Binary $binary 入力バイナリデータ。
	 * @param array{seed?:?int}|null $options
	 * @return Binary ハッシュバイナリ。
	 * @throws CryptoException
	 * @see https://www.php.net/manual/function.hash.php
	 */
	public static function generateHashBinary(string $algorithm, Binary $binary, ?array $options = null): Binary
	{
		return new Binary(self::generateHashCore(true, $algorithm, $binary, $options));
	}

	#endregion
}
