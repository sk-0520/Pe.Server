<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use PeServer\Core\Throws\ArgumentException;
use \Throwable;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\Throws;

/**
 * 暗号化周り
 */
abstract class Cryptography
{
	private const OPTION = 0;
	public const SEPARATOR = '@';
	public const DEFAULT_RANDOM_STRING = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	public const FILE_RANDOM_STRING = '0123456789abcdefghijklmnopqrstuvwxyz';

	/**
	 * 乱数取得。
	 *
	 * `random_int` ラッパー。
	 *
	 * @param integer $max 最大値
	 * @param integer $min 最小値
	 * @return integer
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
	 * ランダムバイトデータを生成。
	 *
	 * `openssl_random_pseudo_bytes` ラッパー。
	 *
	 * @param integer $length
	 * @phpstan-param positive-int $length
	 * @return Binary
	 * @throws CryptoException 失敗
	 * @see https://www.php.net/manual/function.openssl-random-pseudo-bytes.php
	 */
	public static function generateRandomBinary(int $length): Binary
	{
		if ($length < 1) { //@phpstan-ignore-line phpstan:positive-int
			throw new CryptoException('$length: ' . $length);
		}

		$result = openssl_random_pseudo_bytes($length);
		if ($result === false) { //@phpstan-ignore-line
			throw new CryptoException();
		}

		return new Binary($result);
	}

	/**
	 * ランダム文字列を生成。
	 *
	 * @param integer $length
	 * @phpstan-param positive-int $length
	 * @param string $characters
	 * @phpstan-param non-empty-string $characters
	 * @return string
	 * @throws CryptoException 失敗
	 */
	public static function generateRandomString(int $length, string $characters = self::DEFAULT_RANDOM_STRING): string
	{
		if ($length < 1) { //@phpstan-ignore-line phpstan:positive-int
			throw new ArgumentException('$length: ' . $length);
		}
		if (StringUtility::isNullOrWhiteSpace($characters)) { //@phpstan-ignore-line phpstan:positive-int
			throw new ArgumentException('$characters: ' . $characters);
		}

		$charactersArray = StringUtility::toCharacters($characters);

		$min = 0;
		$max = ArrayUtility::getCount($charactersArray) - 1;

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

		$encData = openssl_encrypt($rawValue, $algorithm, $password, self::OPTION, $iv->getRaw());
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
		$values = StringUtility::split($encValue, self::SEPARATOR);
		if (ArrayUtility::getCount($values) !== 3) {
			throw new ArgumentException();
		}
		list($algorithm, $ivBase64, $encData) = $values;

		$iv = Binary::fromBase64($ivBase64);

		/** @var string|false */
		$decData = false;
		try {
			$decData = openssl_decrypt($encData, $algorithm, $password, self::OPTION, $iv->getRaw());
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
	public static function toHashPassword(string $password): string
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * `password_verify(string $password, $hashPassword)` ラッパー。
	 *
	 * @param string $password 生パスワード。
	 * @param string $hashPassword ハッシュ化パスワード。
	 * @return boolean 一致。
	 */
	public static function verifyPassword(string $password, string $hashPassword): bool
	{
		return password_verify($password, $hashPassword);
	}

	/**
	 * ハッシュアルゴリズム一覧。
	 *
	 * `hash_algos` ラッパー。
	 *
	 * @return string[]
	 */
	public static function getHashAlgorithms(): array
	{
		return hash_algos();
	}

	private static function generateHashCore(bool $isBinary, string $algorithm, Binary $binary/*, array $options = []*/): string
	{
		$hash = hash($algorithm, $binary->getRaw(), $isBinary/*, $options */);
		if ($hash === false) { //@phpstan-ignore-line
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
	 * @param Binary $binary
	 * @-param array{seed?:?int} $options
	 * @return string 文字列表現。
	 * @throws CryptoException
	 * @see https://www.php.net/manual/function.hash.php
	 */
	public static function generateHashString(string $algorithm, Binary $binary/*, array $options = []*/): string
	{
		return self::generateHashCore(false, $algorithm, $binary);
	}

	/**
	 * ハッシュ化処理(バイナリ)。
	 *
	 * `hash` ラッパー。
	 *
	 * @param string $algorithm
	 * @phpstan-param non-empty-string $algorithm
	 * @param Binary $binary
	 * @-param array{seed?:?int} $options
	 * @return Binary ハッシュバイナリ。
	 * @throws CryptoException
	 * @see https://www.php.net/manual/function.hash.php
	 */
	public static function generateHashBinary(string $algorithm, Binary $binary/*, array $options = []*/): Binary
	{
		return new Binary(self::generateHashCore(true, $algorithm, $binary));
	}
}
