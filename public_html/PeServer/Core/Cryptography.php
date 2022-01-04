<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\Throws;

/**
 * 暗号化周り
 */
abstract class Cryptography
{
	private const OPTION = 0;
	private const SEPARATOR = '@';

	/**
	 * ランダムバイト文字列を生成。
	 *
	 * @param integer $length
	 * @return string
	 */
	public static function generateRandomBytes(int $length): string
	{
		$result = openssl_random_pseudo_bytes($length);
		if ($result === false) {
			throw new CryptoException();
		}

		return $result;
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
		$iv = openssl_random_pseudo_bytes($ivLength);
		if ($iv === false) { //@phpstan-ignore-line
			throw new CryptoException($algorithm);
		}
		$ivBase64 = base64_encode($iv);

		$encData = openssl_encrypt($data, $algorithm, $password, self::OPTION, $iv);
		if ($encData === false) {
			throw new CryptoException();
		}

		return $algorithm . self::SEPARATOR . $ivBase64 . self::SEPARATOR . $encData;
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

		$iv = base64_decode($ivBase64);
		if ($iv === false) { //@phpstan-ignore-line
			throw new CryptoException();
		}

		/** @var string|false */
		$decData = false;
		try {
			$decData = openssl_decrypt($encData, $algorithm, $password, self::OPTION, $iv);
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
