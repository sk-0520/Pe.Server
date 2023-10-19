<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mail\SendMode;

interface IMailSetting
{
	#region function

	/**
	 * メール送信方法。
	 *
	 * @return SendMode
	 */
	public function mode(): SendMode;

	#endregion
}
