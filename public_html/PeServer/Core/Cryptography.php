<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\Throws;
use Throwable;

/**
 * 暗号化周り
 */
abstract class Cryptography
{
	private const OPTION = 0;
	private const SEPARATOR = '@';

	/**
	 * ランダムバイトデータを生成。
	 *
	 * @param integer $length
	 * @return Bytes
	 */
	public static function generateRandomBytes(int $length): Bytes
	{
		$result = openssl_random_pseudo_bytes($length);
		if ($result === false) { //@phpstan-ignore-line
			throw new CryptoException();
		}

		return new Bytes($result);
	}

	/**
	 * 乱数取得。
	 *
	 * `random_int` ラッパー。
	 *
	 * @param integer $max 最大値
	 * @param integer $min 最小値
	 * @return integer
	 * @throws CryptoException
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
	 * 文字列を暗号化。
	 *
	 * @param string $data 生文字列。
	 * @param string $algorithm 暗号化方法。
	 * @param string $password パスワード。
	 * @return string 暗号化された文字列。 アルゴリズム@IV@暗号化データ となる。
	 * @throws CryptoException 失敗
	 */
	public static function encrypt(string $data, string $algorithm, string $password): string
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

		$iv = self::generateRandomBytes($ivLength);

		$encData = openssl_encrypt($data, $algorithm, $password, self::OPTION, $iv->getRaw());
		if ($encData === false) {
			throw new CryptoException();
		}

		return $algorithm . self::SEPARATOR . $iv->toBase64() . self::SEPARATOR . $encData;
	}

	/**
	 * Cryptography::encrypt で暗号化されたデータの復元。
	 *
	 * @param string $data 暗号化データ。
	 * @param string $password パスワード。
	 * @return string 生文字列。
	 * @throws CryptoException 失敗
	 */
	public static function decrypt(string $data, string $password): string
	{
		$values = StringUtility::split($data, self::SEPARATOR);
		if (ArrayUtility::getCount($values) !== 3) {
			throw new CryptoException();
		}
		list($algorithm, $ivBase64, $encData) = $values;

		$iv = Bytes::fromBase64($ivBase64);

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
	 * password_hash($password, PASSWORD_DEFAULT) のラップ
	 *
	 * @param string $password 生パスワード。
	 * @return string ハッシュ化パスワード。
	 */
	public static function toHashPassword(string $password): string
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * password_verify(string $password, $hashPassword) のラップ。
	 *
	 * @param string $password 生パスワード。
	 * @param string $hashPassword ハッシュ化パスワード。
	 * @return boolean 一致。
	 */
	public static function verifyPassword(string $password, string $hashPassword): bool
	{
		return password_verify($password, $hashPassword);
	}
}
