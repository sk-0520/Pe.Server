<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use Closure;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mail\SendMode;

/**
 * 疑似送信設定。
 *
 * @codeCoverageIgnore
 */
readonly class SmtpDumpSetting extends SmtpSetting implements IDumpSetting
{
	use MailDumpTrait;

	/**
	 *
	 * @param string $host
	 * @param int $port
	 * @param string $secure
	 * @param bool $authentication
	 * @param string $userName
	 * @param string $password
	 * @param array{file:string,send?:bool} $dumpOptions
	 */
	public function __construct(
		public string $host,
		public int $port,
		public string $secure,
		public bool $authentication,
		public string $userName,
		public string $password,
		array $dumpOptions
	) {
		parent::__construct($host, $port, $secure, $authentication, $userName, $password);
		$this->dumpOptions = $dumpOptions;
	}

	#region SmtpSetting

	public function mode(): SendMode
	{
		return SendMode::Dump;
	}

	#endregion
}
