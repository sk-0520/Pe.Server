<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Store\ISessionHandlerFactory;

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
	/**
	 * @var ?class-string<ISessionHandlerFactory>
	 */
	public ?string $handlerFactory;
	public CookieStoreSetting $cookie;

	#endregion
}
