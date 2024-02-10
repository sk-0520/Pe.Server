<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\PersistenceSetting;

/**
 * その他設定。
 *
 * @immutable
 */
class ConfigurationSetting
{
	#region variable

	public ConfirmSetting $confirm;

	public AddressSetting $address;

	#endregion
}
