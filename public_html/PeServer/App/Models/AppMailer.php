<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Environment;
use PeServer\Core\Mailer;
use PeServer\Core\StringUtility;

/**
 * アプリケーション側で使用するメール送信機能。
 */
final class AppMailer extends Mailer
{
	private string $overwriteTarget = '';

	public function __construct()
	{
		parent::__construct(AppConfiguration::$config['mail']);

		$this->fromAddress = AppConfiguration::$config['config']['address']['from_email'];
		$this->returnPath = AppConfiguration::$config['config']['address']['return_email'];

		if (!Environment::isProduction() && isset(AppConfiguration::$config['debug'])) {
			$target = ArrayUtility::getOr(AppConfiguration::$config['debug'], 'mail_overwrite_target', '');
			if (!StringUtility::isNullOrWhiteSpace($target)) {
				$this->overwriteTarget = $target;
			}
		}
	}

	protected function convertAddress(int $kind, array $data): array
	{
		$result = parent::convertAddress($kind, $data);

		if ($kind != parent::ADDRESS_KIND_TO || StringUtility::isNullOrWhiteSpace($this->overwriteTarget)) {
			return $result;
		}

		// 宛先を差し替え
		$src = $result[0];
		$result[0] = $this->overwriteTarget;
		$result[1] = $result[1] . '[差し替え]' . $result[0];

		return $result;
	}

	protected function getSubject(string $subject): string
	{
		return '[Pe.Server] ' . $subject;
	}
}
