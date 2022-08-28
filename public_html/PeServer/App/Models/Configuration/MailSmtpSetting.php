<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Serialization\Mapping;

/**
 * SMTP設定。
 *
 * @immutable
 */
class MailSmtpSetting
{
	#region variable

	public string $host;
	public int $port;
	public string $secure;
	public bool $authentication;
	#[Mapping(name: 'user_name')]
	public string $userName;
	public string $password;

	#endregion
}
