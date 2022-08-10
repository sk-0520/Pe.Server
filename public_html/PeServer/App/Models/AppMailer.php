<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Environment;
use PeServer\Core\DefaultValue;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Text;

/**
 * アプリケーション側で使用するメール送信機能。
 */
final class AppMailer extends Mailer
{
	private string $overwriteTarget = DefaultValue::EMPTY_STRING;

	public function __construct()
	{
		parent::__construct(AppConfiguration::$config['mail']);

		$fromEmail = AppConfiguration::$config['config']['address']['from_email'];
		$this->fromAddress = new EmailAddress($fromEmail['address'], $fromEmail['name']);
		$this->returnPath = AppConfiguration::$config['config']['address']['return_email'];

		if (!Environment::isProduction() && isset(AppConfiguration::$config['debug'])) {
			$target = ArrayUtility::getOr(AppConfiguration::$config['debug'], 'mail_overwrite_target', '');
			if (!Text::isNullOrWhiteSpace($target)) {
				$this->overwriteTarget = $target;
			}
		}
	}

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
		return '[Pe.Server] ' . $subject;
	}
}
