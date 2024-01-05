<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\LoggerSetting;
use PeServer\Core\Serialization\Mapping;

/**
 * ログ設定。
 *
 * @immutable
 */
class LoggingSetting
{
	#region variable

	/**
	 * ロガー設定。
	 *
	 * @var array<string,LoggerSetting>
	 */
	#[Mapping(arrayValueClassName: LoggerSetting::class)]
	public array $loggers = []; //@phpstan-ignore-line [CODE_READONLY]

	#[Mapping(name: 'archive_size')]
	public int $archiveSize;

	#endregion
}
