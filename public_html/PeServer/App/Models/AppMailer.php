<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Environment;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mail\SmtpSetting;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * アプリケーション側で使用するメール送信機能。
 */
final class AppMailer extends Mailer
{
	#region variable

	private string $overwriteTarget = Text::EMPTY;
	public string $customSubjectHeader = '';

	#endregion

	public function __construct(AppConfiguration $config)
	{
		$mailSetting = match ($config->setting->mail->mode) {
			'smtp' => new SmtpSetting(
				$config->setting->mail->smtp->host,
				$config->setting->mail->smtp->port,
				$config->setting->mail->smtp->secure,
				$config->setting->mail->smtp->authentication,
				$config->setting->mail->smtp->userName,
				$config->setting->mail->smtp->password
			),
			default => throw new ArgumentException($config->setting->mail->mode),
		};
		parent::__construct($mailSetting);

		$fromEmail = $config->setting->config->address->fromEmail;
		$this->fromAddress = new EmailAddress($fromEmail->address, $fromEmail->name);
		$this->returnPath = $config->setting->config->address->returnEmail;

		if (!Environment::isProduction() && isset($config->setting->debug)) {
			$target = $config->setting->debug->mailOverwriteTarget;
			if (!Text::isNullOrWhiteSpace($target)) {
				$this->overwriteTarget = $target;
			}
		}
	}

	#region Mailer

	protected function convertAddress(int $kind, EmailAddress $data): EmailAddress
	{
		$result = parent::convertAddress($kind, $data);

		if ($kind != parent::ADDRESS_KIND_TO || Text::isNullOrWhiteSpace($this->overwriteTarget)) {
			return $result;
		}

		// 宛先を差し替え
		return new EmailAddress(
			$this->overwriteTarget,
			$result->name . '[差し替え]' . $data->address
		);
	}

	protected function buildSubject(string $subject): string
	{
		$customSubjectHeader = Text::isNullOrWhiteSpace($this->customSubjectHeader)
			? '[Pe.Server]'
			: $this->customSubjectHeader;

		return $customSubjectHeader . ' ' . $subject;
	}

	#endregion
}
