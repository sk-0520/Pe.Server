<?php

declare(strict_types=1);

namespace PeServer\Core;

use Exception;
use Throwable;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Errors\ErrorHandler;
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
	 * @param int $min 最小値。
	 * @param int $max 最大値。
	 * @return int 乱数。
	 * @throws CryptoException 失敗
	 * @see https://www.php.net/manual/function.random-int.php
	 */
	public static function generateRandomInteger(int $min, int $max): int
	{
		try {
			/** @disregard P1010 */
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
	 * @param int $length バイト数。
	 * @phpstan-param positive-int $length
	 * @return Binary バイナリデータ。
	 * @throws ArgumentException 失敗
	 * @throws CryptoException 失敗
	 * @see https://www.php.net/manual/function.openssl-random-pseudo-bytes.php
	 */
	public static function generateRandomBinary(int $length): Binary
	{
		if ($length < 1) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$length: ' . $length);
		}

		try {
			$result = openssl_random_pseudo_bytes($length);
			return new Binary($result);
		} catch (Throwable $ex) {
			Throws::reThrow(CryptoException::class, $ex);
		}
	}

	/**
	 * ランダム文字列を生成。
	 *
	 * @param int $length 文字列長。
	 * @phpstan-param positive-int $length
	 * @param non-empty-string $characters ランダム文字の元になる文字列。
	 * @return string 文字列。
	 * @throws CryptoException 失敗
	 */
	public static function generateRandomString(int $length, string $characters = self::DEFAULT_RANDOM_STRING): string
	{
		if ($length < 1) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$length: ' . $length);
		}
		if (Text::isNullOrEmpty($characters)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$characters: empty');
		}

		$charactersArray = Text::toCharacters($characters);

		$min = 0;
		$max = Arr::getCount($charactersArray) - 1;

		$result = '';

		for ($i = 0; $i < $length; $i++) {
			$index = self::generateRandomInteger($min, $max);
			$result .= $charactersArray[$index];
		}

		return $result;
	}

	/**
	 * 文字列を暗号化。
	 *
	 * @param non-empty-string $algorithm 暗号化方法。
	 * @param string $rawValue 生文字列。
	 * @param string $password パスワード。
	 * @return string 暗号化された文字列。 アルゴリズム@IV@暗号化データ となる。
	 * @throws CryptoException 失敗
	 */
	public static function encrypt(string $algorithm, string $rawValue, string $password): string
	{
		$result = ErrorHandler::trap(fn() => openssl_cipher_iv_length($algorithm));
		if ($result->isFailureOrFalse()) {
			throw new CryptoException($algorithm);
		}

		$ivLength = $result->value;
		assert(0 < $ivLength); // こんなんきちんと考慮する必要ないわ

		$iv = self::generateRandomBinary($ivLength);

		$result = ErrorHandler::trap(fn() => openssl_encrypt($rawValue, $algorithm, $password, self::OPTION, $iv->raw));
		if ($result->isFailureOrFalse()) {
			throw new CryptoException($algorithm);
		}

		return $algorithm . self::SEPARATOR . $iv->toBase64() . self::SEPARATOR . $result->value;
	}

	/**
	 * `Cryptography::encrypt` で暗号化されたデータの復元。
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

		$result = ErrorHandler::trap(fn() => openssl_decrypt($encData, $algorithm, $password, self::OPTION, $iv->raw));
		if ($result->isFailureOrFalse()) {
			throw new CryptoException($algorithm);
		}

		return $result->value;
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
	public static function needsRehashPassword(string $hashPassword): bool
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
		//@phpstan-ignore-next-line [DOCTYPE]
		return password_get_info($hashPassword);
	}

	/**
	 * `password_algos` ラッパー。
	 *
	 * @return list<string>
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
	 * @return list<string>
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
	 * @param array{seed?:?int} $options
	 * @return non-empty-string
	 */
	private static function generateHashCore(bool $isBinary, string $algorithm, Binary $binary, array $options = []): string
	{
		try {
			$hash = hash($algorithm, $binary->raw, $isBinary, $options);
		} catch (Throwable $ex) {
			Throws::reThrow(CryptoException::class, $ex);
		}

		return $hash;
	}

	/**
	 * ハッシュ化処理(文字列)。
	 *
	 * `hash` ラッパー。
	 *
	 * @param non-empty-string $algorithm
	 * @param Binary $binary 入力バイナリデータ。
	 * @param array{seed?:?int} $options
	 * @return non-empty-string 文字列表現。
	 * @throws CryptoException
	 * @see https://www.php.net/manual/function.hash.php
	 */
	public static function generateHashString(string $algorithm, Binary $binary, array $options = []): string
	{
		return self::generateHashCore(false, $algorithm, $binary, $options);
	}

	/**
	 * ハッシュ化処理(バイナリ)。
	 *
	 * `hash` ラッパー。
	 *
	 * @param non-empty-string $algorithm アルゴリズム。
	 * @param Binary $binary 入力バイナリデータ。
	 * @param array{seed?:?int} $options
	 * @return Binary ハッシュバイナリ。
	 * @throws CryptoException
	 * @see https://www.php.net/manual/function.hash.php
	 */
	public static function generateHashBinary(string $algorithm, Binary $binary, array $options = []): Binary
	{
		return new Binary(self::generateHashCore(true, $algorithm, $binary, $options));
	}

	#endregion
}
