<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\DefaultValue;
use PeServer\Core\Serialization\Mapping;

/**
 * デバッグ設定。
 *
 * @immutable
 */
class DebugSetting
{
	#region variable

	#[Mapping(name: 'mail_overwrite_target')]
	public string $mailOverwriteTarget = DefaultValue::EMPTY_STRING;

	#endregion
}
