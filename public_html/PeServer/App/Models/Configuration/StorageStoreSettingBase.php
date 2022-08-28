<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

/**
 * ストレージが必要なストア系設定。
 *
 * @immutable
 */
abstract class StorageStoreSettingBase
{
	#region variable

	public string $name;
	public string $save;
	public CookieStoreSetting $cookie;

	#endregion
}
