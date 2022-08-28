<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

/**
 * Eメールアドレス設定。
 *
 * @immutable
 */
class EmailAddressSetting
{
	#region variable

	public string $name;
	public string $address;

	#endregion
}
