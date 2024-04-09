<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Serialization\Mapping;

/**
 * アドレス設定。
 *
 * @immutable
 */
class ApiSetting
{
	#region variable

	#[Mapping("release_url")]
	public string $releaseUrl;

	#endregion
}
