<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Cryptography;
use PeServer\Core\Throws\CryptoException;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Binary;
use PeServer\Core\StringUtility;

abstract class AppCryptography
{
	/**
	 * アプリケーション設定から文字列暗号化。
	 *
	 * @param string $data
	 * @return string DB格納値を想定。
	 */
	public static function encrypt(string $data): string
	{
		$crypto = AppConfiguration::$config['crypto'];
		return Cryptography::encrypt($data, $crypto['algorithm'], $crypto['password']);
	}

	/**
	 * アプリケーション設定から文字列復号化。
	 *
	 * @param string $data DB格納値を想定。
	 * @return string アプリ使用文字列を想定。
	 */
	public static function decrypt(string $data): string
	{
		$crypto = AppConfiguration::$config['crypto'];
		return Cryptography::decrypt($data, $crypto['password']);
	}

	public static function encryptToken(string $data): string
	{
		$token = AppConfiguration::$config['crypto']['token'];
		$value = Cryptography::encrypt($data, $token['algorithm'], $token['password']);
		return StringUtility::split($value, Cryptography::SEPARATOR, 2)[1];
	}

	public static function decryptToken(string $data): string
	{
		$token = AppConfiguration::$config['crypto']['token'];
		$value = $data;
		return Cryptography::decrypt($token['algorithm'] . Cryptography::SEPARATOR . $value, $token['password']);
	}

	/**
	 * 検索用マーカー整数への変換。
	 *
	 * 一意性はないので暗号化データの絞り込みに使用する想定。
	 *
	 * @param string $data
	 * @return integer
	 */
	public static function toMark(string $data): int
	{
		$crypto = AppConfiguration::$config['crypto'];
		$input = $data . $crypto['pepper'];

		$binary = Cryptography::generateHashBinary('fnv132', new Binary($input));

		$result = unpack('N', $binary->getRaw(), 0);
		if ($result === false) {
			throw new CryptoException();
		}

		return $result[1];
	}
}
