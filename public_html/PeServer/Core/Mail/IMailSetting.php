<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Mail\Mailer;

interface IMailSetting
{
	#region function

	/**
	 * メール送信方法。
	 *
	 * @return int
	 * @phpstan-return Mailer::SEND_MODE_*
	 */
	function mode(): int;

	#endregion
}
