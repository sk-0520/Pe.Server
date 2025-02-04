<?php

declare(strict_types=1);

namespace PeServer\App\Cli;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppMailer;
use PeServer\Core\Cli\CliApplicationBase;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\DI\Inject;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\StaticRamLogger;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use Throwable;

abstract class AppApplicationBase extends CliApplicationBase
{
	#region variable

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private IDatabaseConnection $databaseConnection;

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private AppConfiguration $config;

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private AppMailer $mailer;

	#endregion

	public function __construct(ILoggerFactory $loggerFactory)
	{
		parent::__construct($loggerFactory);
	}

	#region function

	/**
	 * データベース接続処理。
	 *
	 * @return DatabaseContext
	 */
	protected function openDatabase(): DatabaseContext
	{
		return $this->databaseConnection->open();
	}

	#endregion

	#region CliApplicationBase

	protected function failure(): void
	{
		$this->mailer->customSubjectHeader = "[CRON-ERROR] " . TypeUtility::getSimpleClassName($this);
		$this->mailer->setMessage(new EmailMessage(
			Text::join(PHP_EOL, StaticRamLogger::$logs)
		));

		foreach ($this->config->setting->config->address->notify->maintenance as $email) {
			$this->mailer->toAddresses = [
				new EmailAddress($email),
			];

			$this->logger->info("{0}", $this->mailer);

			try {
				$this->mailer->send();
			} catch (Throwable $ex) {
				$this->logger->error($ex);
			}
		}
	}

	#endregion
}
