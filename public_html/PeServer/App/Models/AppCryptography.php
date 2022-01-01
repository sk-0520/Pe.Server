<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Cryptography;
use PeServer\Core\Throws\CoreException;

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
		$crypto = AppConfiguration::$json['crypto'];
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
		$crypto = AppConfiguration::$json['crypto'];
		return Cryptography::decrypt($data, $crypto['password']);
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
		$crypto = AppConfiguration::$json['crypto'];
		$input = $data . $crypto['pepper'];

		$binary = hash('fnv132', $input, true);
		if ($binary === false) { // @phpstan-ignore-line
			throw new CoreException();
		}

		$result = unpack('N', $binary, 0);
		if ($result === false) {
			throw new CoreException();
		}

		return $result[1];
	}
}
