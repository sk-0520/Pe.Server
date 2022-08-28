<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\PersistenceSetting;

/**
 * ストレージ設定。
 *
 * @immutable
 */
class CacheSetting
{
	#region variable

	public string $database;
	public string $template;
	public string $backup;

	#endregion
}
