<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\CookieStoreSetting;
use PeServer\App\Models\Configuration\SessionStoreSetting;
use PeServer\App\Models\Configuration\TemporaryStoreSetting;

/**
 * ストア設定。
 *
 * @immutable
 */
class StoreSetting
{
	#region variable

	public CookieStoreSetting $cookie;
	public TemporaryStoreSetting $temporary;
	public SessionStoreSetting $session;

	#endregion
}
