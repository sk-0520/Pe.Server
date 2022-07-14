<?php

declare(strict_types=1);

namespace PeServer\Core;

require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/Exception.php');
//require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/OAuth.php');
require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/PHPMailer.php');
//require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/POP3.php');
require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/SMTP.php');

use \PHPMailer\PHPMailer\PHPMailer;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\InitialValue;
use PeServer\Core\EmailAddress;

class Mailer
{
	private const SEND_MODE_SMTP = 1;

	protected const ADDRESS_KIND_FROM = 1;
	protected const ADDRESS_KIND_TO = 2;
	protected const ADDRESS_KIND_CC = 3;
	protected const ADDRESS_KIND_BCC = 4;

	/**
	 * @var integer
	 * @phpstan-var self::SEND_MODE_*
	 * @readonly
	 */
	private int $sendMode;
	/**
	 * @readonly
	 */
	private LocalSmtpSetting $smtp;

	public string $encoding  = '8bit';
	public string $characterSet = 'utf-8';

	/**
	 * Return-Path:
	 */
	public string $returnPath = InitialValue::EMPTY_STRING;
	/**
	 * FROM:
	 */
	public EmailAddress $fromAddress;
	/**
	 * TO:
	 *
	 * @var EmailAddress[]
	 */
	public array $toAddresses = [];
	/**
	 * CC:
	 *
	 * @var EmailAddress[]
	 */
	public array $ccAddresses = [];
	/**
	 * BCC:
	 *
	 * @var EmailAddress[]
	 */
	public array $bccAddresses = [];

	/**
	 * 件名。
	 */
	public string $subject = InitialValue::EMPTY_STRING;

	/**
	 * メッセージ。
	 */
	private EmailMessage $message;

	/**
	 * 生成。
	 *
	 * @param array{mode:string,smtp?:array{host:string,port:int,secure:string,authentication:bool,user_name:string,password:string}} $setting
	 */
	public function __construct(array $setting)
	{
		$this->fromAddress = new EmailAddress(InitialValue::EMPTY_STRING, InitialValue::EMPTY_STRING);
		$this->message = new EmailMessage();

		switch ($setting['mode']) {
			case 'smtp': {
					if (!isset($setting['smtp'])) {
						throw new ArgumentNullException('smtp');
					}

					$smtpSetting = $setting['smtp'];

					$smtp = new LocalSmtpSetting(
						$smtpSetting['host'],
						$smtpSetting['port'],
						$smtpSetting['secure'],
						$smtpSetting['authentication'],
						$smtpSetting['user_name'],
						$smtpSetting['password']
					);

					$this->sendMode = self::SEND_MODE_SMTP;
					$this->smtp = $smtp;
				}
				break;

			default:
				throw new NotImplementedException();
		}
	}

	/**
	 * 本文設定。
	 *
	 * @param EmailMessage $message
	 * @return void
	 * @throws ArgumentException HTMLとプレーンテキスト未設定。
	 */
	public function setMessage(EmailMessage $message)
	{
		if (!$message->isText() && !$message->isHtml()) {
			throw new ArgumentException();
		}

		$this->message = $message;
	}

	/**
	 * アドレスを設定可能形式に変換。
	 *
	 * サービス側でトラップせずにこいつを拡張して開発中は知らんところに飛ばないように調整する。
	 *
	 * @param int $kind 種別
	 * @phpstan-param self::ADDRESS_KIND_* $kind 種別
	 * @param EmailAddress $data
	 * @return EmailAddress
	 * @throws ArgumentException そもそものアドレスが未設定
	 */
	protected function convertAddress(int $kind, EmailAddress $data): EmailAddress
	{
		if (StringUtility::isNullOrWhiteSpace($data->address)) {
			throw new ArgumentException('$data->address');
		}

		return $data;
	}

	/**
	 * 件名を調整。
	 *
	 * @param string $subject 元になる件名。
	 * @return string
	 */
	protected function buildSubject(string $subject): string
	{
		return $subject;
	}

	/**
	 * 送信。
	 *
	 * @return void
	 */
	public function send(): void
	{
		$client = new PHPMailer(true);

		$lang = mb_language();
		if (is_string($lang)) {
			$client->setLanguage($lang, __DIR__ . '/../Libs/PHPMailer/language');
		}

		$client->CharSet = $this->characterSet;
		$client->Encoding = $this->encoding;

		$client->Sender = $this->returnPath;
		$fromAddress = $this->convertAddress(self::ADDRESS_KIND_FROM, $this->fromAddress);
		$client->setFrom($fromAddress->address, $fromAddress->name);
		if (StringUtility::isNullOrWhiteSpace($client->Sender)) {
			$client->Sender = $client->$this->fromAddress['address'];
		}


		$client->clearAddresses();
		foreach ($this->toAddresses as $address) {
			$toAddress = $this->convertAddress(self::ADDRESS_KIND_TO, $address);
			$client->addAddress($toAddress->address, $toAddress->name);
		}

		$client->clearCCs();
		foreach ($this->ccAddresses as $address) {
			$ccAddress = $this->convertAddress(self::ADDRESS_KIND_CC, $address);
			$client->addCC($ccAddress->address, $ccAddress->name);
		}

		$client->clearBCCs();
		foreach ($this->bccAddresses as $address) {
			$bccAddress = $this->convertAddress(self::ADDRESS_KIND_BCC, $address);
			$client->addBCC($bccAddress->address, $bccAddress->name);
		}


		$isHtml = false;
		$client->Subject = $this->buildSubject($this->subject);
		if ($this->message->isHtml()) {
			$client->isHTML(true);
			$client->Body = $this->message->getHtml();
			$isHtml = true;
		}
		if ($this->message->isText()) {
			if ($isHtml) {
				$client->AltBody = $this->message->getText();
			} else {
				$client->Body = $this->message->getText();
			}
		} else if (!$isHtml) {
			throw new InvalidOperationException();
		}

		switch ($this->sendMode) {
			case self::SEND_MODE_SMTP: {
					$client->isSMTP();
					$client->Host = $this->smtp->host;
					$client->Port = $this->smtp->port;
					$client->SMTPSecure = $this->smtp->secure;
					$client->SMTPAuth = $this->smtp->authentication;
					$client->Username = $this->smtp->userName;
					$client->Password = $this->smtp->password;
				}
				break;

			default:
				throw new NotImplementedException();
		}

		$client->send();
	}
}

/**
 * SMTP設定。
 *
 * @immutable
 */
final class LocalSmtpSetting
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
}
