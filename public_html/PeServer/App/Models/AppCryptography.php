<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Cryptography;
use PeServer\Core\Throws\CryptoException;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Binary;
use PeServer\Core\Text;

final class AppCryptography
{
	public function __construct(
		private AppConfiguration $config
	) {
	}

	#region function

	/**
	 * アプリケーション設定から文字列暗号化。
	 *
	 * @param string $data
	 * @return string DB格納値を想定。
	 */
	public function encrypt(string $data): string
	{
		$crypto = $this->config->setting->crypto;
		return Cryptography::encrypt($crypto->algorithm, $data, $crypto->password);
	}

	/**
	 * アプリケーション設定から文字列復号化。
	 *
	 * @param string $data DB格納値を想定。
	 * @return string アプリ使用文字列を想定。
	 */
	public function decrypt(string $data): string
	{
		$crypto = $this->config->setting->crypto;
		return Cryptography::decrypt($data, $crypto->password);
	}

	public function encryptToken(string $data): string
	{
		$token = $this->config->setting->crypto->token;
		$value = Cryptography::encrypt($token->algorithm, $data, $token->password);
		return Text::split($value, Cryptography::SEPARATOR, 2)[1];
	}

	public function decryptToken(string $data): string
	{
		$token = $this->config->setting->crypto->token;
		$value = $data;
		return Cryptography::decrypt($token->algorithm . Cryptography::SEPARATOR . $value, $token->password);
	}

	/**
	 * 検索用マーカー整数への変換。
	 *
	 * 一意性はないので暗号化データの絞り込みに使用する想定。
	 *
	 * @param string $data
	 * @return int
	 */
	public function toMark(string $data): int
	{
		$crypto = $this->config->setting->crypto;
		$input = $data . $crypto->pepper;

		$binary = Cryptography::generateHashBinary('fnv132', new Binary($input));

		$result = unpack('N', $binary->raw, 0);
		if ($result === false) {
			throw new CryptoException();
		}

		return $result[1];
	}

	#endregion
}
