<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Serialization\Mapping;

/**
 * メール設定。
 *
 * @immutable
 */
class MailSetting
{
	#region variable

	public string $mode;

	public MailSmtpSetting $smtp;

	#endregion
}
