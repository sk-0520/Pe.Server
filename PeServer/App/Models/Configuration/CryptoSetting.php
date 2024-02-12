<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

/**
 * 暗号設定。
 *
 * @immutable
 */
class CryptoSetting
{
	#region variable

	/**
	 * アルゴリズム。
	 *
	 * @var non-empty-string
	 */
	public string $algorithm;
	public string $password;
	public string $pepper;

	public CryptoTokenSetting $token;

	#endregion
}
