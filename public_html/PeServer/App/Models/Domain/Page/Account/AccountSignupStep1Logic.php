<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\UrlUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Cryptography;
use PeServer\Core\EmailAddress;
use PeServer\Core\InitialValue;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\UserUtility;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\Core\EmailMessage;

class AccountSignupStep1Logic extends PageLogicBase
{
	private const TEMP_TOKEN = 'sign_up_token';

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'value',
			'account_signup_token',
			'account_signup_value',
			'account_signup_email',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('account_signup_email', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isEmail($key, $value);
		});

		$temp = $this->popTemporary(self::TEMP_TOKEN);
		$tempValue = ArrayUtility::getOr($temp, 'value', InitialValue::EMPTY_STRING);
		$tempToken = ArrayUtility::getOr($temp, 'token', InitialValue::EMPTY_STRING);

		$inputValue = $this->getRequest('account_signup_value');
		$inputToken = $this->getRequest('account_signup_token');

		if (!($tempValue === $inputValue && $tempToken == $inputToken)) {
			$this->addError('account_signup_value', I18n::message('error/sign_up_token'));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$email = $this->getRequest('account_signup_email');
		$token = UserUtility::generateSignupToken();

		$params = [
			'token' => $token,
			'raw_email' => $email,
			'email' => AppCryptography::encrypt($email),
			'mark_email' => AppCryptography::toMark($email),
		];

		$this->logger->info('ユーザー登録処理開始: {0}', $params['token']);

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context, $params) {
			$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($context);

			$likeItems = $signUpWaitEmailsEntityDao->selectLikeEmails($params['mark_email']);
			foreach ($likeItems as $likeItem) {
				$rawLikeEmail = AppCryptography::decrypt($likeItem['email']);
				if ($rawLikeEmail === $params['raw_email']) {
					$this->logger->info('重複メールアドレスを破棄: {0}', $likeItem['token']);
					$signUpWaitEmailsEntityDao->deleteToken($likeItem['token']);
				}
			}

			$signUpWaitEmailsEntityDao->insertEmail(
				$params['token'],
				$params['email'],
				$params['mark_email'],
				ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', InitialValue::EMPTY_STRING),
				ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', InitialValue::EMPTY_STRING)
			);

			return true;
		}, $params);

		//TODO: 設定からとるのかリダイレクトみたいにサーバーからとるのか混在中
		$baseUrl = StringUtility::replaceMap(
			AppConfiguration::$config['config']['address']['public_url'],
			[
				'DOMAIN' => AppConfiguration::$config['config']['address']['domain']
			]
		);
		$url = UrlUtility::joinPath($baseUrl, "account/signup/$token");

		$subject = I18n::message('subject/sign_up_step1');
		$values = [
			'url' => $url,
		];
		$html = AppTemplate::createMailTemplate('mail_signup_step1', $subject, $values);

		$mailer = new AppMailer();
		$mailer->toAddresses = [
			new EmailAddress($email),
		];
		$mailer->subject = $subject;
		$mailer->setMessage(new EmailMessage(null, $html));

		$mailer->send();

		$this->result['token'] = $token;
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		if ($callMode->isSubmit() && ArrayUtility::existsKey($this->result, 'token')) {
			$this->removeTemporary(self::TEMP_TOKEN);
			return;
		}

		$tempToken = Cryptography::generateRandomBytes(10)->toHex();
		$tempValue = sprintf('%04d', Cryptography::generateRandomInteger(9999));
		$this->pushTemporary(self::TEMP_TOKEN, [
			'token' => $tempToken,
			'value' => $tempValue,
		]);
		$this->setValue('account_signup_token', $tempToken);
		$this->setValue('account_signup_value', InitialValue::EMPTY_STRING);
		$this->setValue('value', $tempValue);
	}
}
