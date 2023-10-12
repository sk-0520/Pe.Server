<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Mail\Mailer;

/**
 * SMTP送信設定。
 */
readonly class SmtpSetting implements IMailSetting
{
	public function __construct(
		public string $host,
		public int $port,
		public string $secure,
		public bool $authentication,
		public string $userName,
		public string $password
	) {
	}

	#region IMailSetting

	public function mode(): int
	{
		return Mailer::SEND_MODE_SMTP;
	}

	#endregion
}
