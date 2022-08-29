<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use \PHPMailer\PHPMailer\PHPMailer;
use PeServer\Core\DefaultValue;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotImplementedException;

class Mailer
{
	#region define

	public const SEND_MODE_SMTP = 1;

	protected const ADDRESS_KIND_FROM = 1;
	protected const ADDRESS_KIND_TO = 2;
	protected const ADDRESS_KIND_CC = 3;
	protected const ADDRESS_KIND_BCC = 4;

	public const DEFAULT_ENCODING = '8bit';
	public const DEFAULT_CHARACTER_SET = 'utf-8';

	#endregion

	#region variable

	/**
	 * @readonly
	 */
	private IMailSetting $setting;

	public string $encoding  = self::DEFAULT_ENCODING;
	public string $characterSet = self::DEFAULT_CHARACTER_SET;

	/**
	 * Return-Path:
	 */
	public string $returnPath = DefaultValue::EMPTY_STRING;
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
	public string $subject = DefaultValue::EMPTY_STRING;

	/**
	 * メッセージ。
	 */
	private EmailMessage $message;

	#endregion

	/**
	 * 生成。
	 *
	 * @param IMailSetting $setting
	 */
	public function __construct(IMailSetting $setting)
	{
		$this->fromAddress = new EmailAddress(DefaultValue::EMPTY_STRING, DefaultValue::EMPTY_STRING);
		$this->message = new EmailMessage();
		$this->setting = $setting;
	}

	#region function

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
		if (Text::isNullOrWhiteSpace($data->address)) {
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
		if (Text::isNullOrWhiteSpace($client->Sender)) {
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

		if ($this->setting instanceof SmtpSetting) {
			$smtp = $this->setting;
			$client->isSMTP();
			$client->Host = $smtp->host;
			$client->Port = $smtp->port;
			$client->SMTPSecure = $smtp->secure;
			$client->SMTPAuth = $smtp->authentication;
			$client->Username = $smtp->userName;
			$client->Password = $smtp->password;
		}

		$client->send();
	}

	#endregion
}

