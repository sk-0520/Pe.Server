<?php

declare(strict_types=1);

namespace PeServer\Core;

require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/Exception.php');
//require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/OAuth.php');
require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/PHPMailer.php');
//require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/POP3.php');
require_once(__DIR__ . '/../Core/Libs/PHPMailer/src/SMTP.php');

use PeServer\Core\InitialValue;
use \PHPMailer\PHPMailer\PHPMailer;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\InvalidOperationException;

class SmtpSetting
{
	public string $host;
	public int $port;
	public string $secure;
	public bool $authentication;
	public string $userName;
	public string $password;
}

class Mailer
{
	private const SEND_MODE_SMTP = 1;

	protected const ADDRESS_KIND_FROM = 1;
	protected const ADDRESS_KIND_TO = 2;
	protected const ADDRESS_KIND_CC = 3;
	protected const ADDRESS_KIND_BCC = 4;

	private int $sendMode;
	private SmtpSetting $smtp;

	public string $encoding  = '8bit';
	public string $characterSet = 'utf-8';

	/**
	 *
	 */
	public string $returnPath = InitialValue::EMPTY_STRING;
	/**
	 * FROM:
	 *
	 * @var array{address:string,name?:string}
	 */
	public array $fromAddress = ['address' => InitialValue::EMPTY_STRING, 'name' => InitialValue::EMPTY_STRING];
	/**
	 * TO:
	 *
	 * @var array<array{address:string,name?:string}>
	 */
	public array $toAddresses = [];
	/**
	 * CC:
	 *
	 * @var array<array{address:string,name?:string}>
	 */
	public array $ccAddresses = [];
	/**
	 * BCC:
	 *
	 * @var array<array{address:string,name?:string}>
	 */
	public array $bccAddresses = [];

	public string $subject = InitialValue::EMPTY_STRING;

	/**
	 * Undocumented variable
	 *
	 * @var array{text?:string,html?:string}
	 */
	private array $message = [];

	/**
	 * 生成。
	 *
	 * @param array{mode:string,smtp?:array{host:string,port:int,secure:string,authentication:bool,user_name:string,password:string}} $setting
	 */
	public function __construct(array $setting)
	{
		switch ($setting['mode']) {
			case 'smtp': {
					if (!isset($setting['smtp'])) {
						throw new ArgumentNullException('smtp');
					}

					$smtpSetting = $setting['smtp'];

					$smtp = new SmtpSetting();
					$smtp->host = $smtpSetting['host'];
					$smtp->port = $smtpSetting['port'];
					$smtp->secure = $smtpSetting['secure'];
					$smtp->authentication = $smtpSetting['authentication'];
					$smtp->userName = $smtpSetting['user_name'];
					$smtp->password = $smtpSetting['password'];

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
	 * @param array{text?:string,html?:string} $message
	 * @return void
	 * @throws ArgumentException HTMLとプレーンテキスト未設定。
	 */
	public function setMessage(array $message)
	{
		if (!isset($message['text']) && !isset($message['html'])) {
			throw new ArgumentException();
		}

		foreach (['text', 'html'] as $i) {
			if (isset($message[$i])) {
				if (StringUtility::isNullOrWhiteSpace($message[$i])) {
					throw new ArgumentException($i);
				}
			}
		}

		$this->message = $message;
	}

	/**
	 * アドレスを設定可能形式に変換。
	 *
	 * サービス側でトラップせずにこいつを拡張して開発中は知らんところに飛ばないように調整する。
	 *
	 * @param int $kind 種別(ADDRESS_KIND_*)
	 * @param array{address:string,name?:string} $data
	 * @return string[]
	 * @throws ArgumentException そもそものアドレスが未設定
	 */
	protected function convertAddress(int $kind, array $data): array
	{
		if (StringUtility::isNullOrWhiteSpace($data['address'])) {
			throw new ArgumentException('address');
		}

		/** @var string|null */
		$name = ArrayUtility::getOr($data, 'name', null);
		if (StringUtility::isNullOrWhiteSpace($name)) {
			return [$data['address'], InitialValue::EMPTY_STRING];
		}

		return [$data['address'], $data['name']]; // @phpstan-ignore-line getOr
	}

	protected function getSubject(string $subject): string
	{
		return $subject;
	}

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
		$client->setFrom(...$this->convertAddress(self::ADDRESS_KIND_FROM, $this->fromAddress));
		if (StringUtility::isNullOrWhiteSpace($client->Sender)) {
			$client->Sender = $client->$this->fromAddress['address'];
		}


		$client->clearAddresses();
		foreach ($this->toAddresses as $address) {
			$client->addAddress(...$this->convertAddress(self::ADDRESS_KIND_TO, $address));
		}

		$client->clearCCs();
		foreach ($this->ccAddresses as $address) {
			$client->addCC(...$this->convertAddress(self::ADDRESS_KIND_CC, $address));
		}

		$client->clearBCCs();
		foreach ($this->bccAddresses as $address) {
			$client->addBCC(...$this->convertAddress(self::ADDRESS_KIND_BCC, $address));
		}


		$isHtml = false;
		$client->Subject = $this->getSubject($this->subject);
		if (ArrayUtility::tryGet($this->message, 'html', $html)) {
			$client->isHTML(true);
			$client->Body = $html;
			$isHtml = true;
		}
		if (ArrayUtility::tryGet($this->message, 'text', $text)) {
			if ($isHtml) {
				$client->AltBody = $text;
			} else {
				$client->Body = $text;
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
