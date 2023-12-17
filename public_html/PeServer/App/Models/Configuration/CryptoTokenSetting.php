<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

/**
 * 暗号トークン設定。
 *
 * @immutable
 */
class CryptoTokenSetting
{
	#region variable

	/**
	 * アルゴリズム。
	 *
	 * @var non-empty-string
	 */
	public string $algorithm;
	public string $password;

	#endregion
}
