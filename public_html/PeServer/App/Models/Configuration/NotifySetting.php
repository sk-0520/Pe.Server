<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Serialization\Mapping;


/**
 * SMTP設定。
 *
 * @immutable
 */
class NotifySetting
{
	#region variable

	/**
	 * クラッシュレポート送信先。
	 *
	 * @var string[]
	 */
	#[Mapping('crash_report')]
	public array $crashReport;

	/**
	 * フィードバック送信先。
	 *
	 * @var string[]
	 */
	public array $feedback;

	#endregion
}
