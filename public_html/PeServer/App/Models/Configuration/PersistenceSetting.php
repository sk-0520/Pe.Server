<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

/**
 * 永続化設定。
 *
 * @immutable
 */
class PersistenceSetting
{
	#region variable

	public DatabaseSetting $default;

	#endregion
}
