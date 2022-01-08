<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\I18n;
use PeServer\Core\Uuid;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\AppMailer;
use PeServer\Core\Mvc\Validations;
use PeServer\App\Models\AppTemplate;
use PeServer\Core\Database\Database;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppCryptography;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domains\AccountValidator;
use PeServer\App\Models\Dao\Domains\UserDomainDao;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\Core\Cryptography;
use PeServer\Core\UrlUtility;

class AccountSignupStep1Logic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
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
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$email = $this->getRequest('account_signup_email');
		$token = Cryptography::generateRandomBytes(40)->toHex();

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
				ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', ''),
				ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', '')
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
			['address' => $email],
		];
		$mailer->subject = $subject;
		$mailer->setMessage([
			'html' => $html,
		]);

		$mailer->send();

		$this->result['token'] = $token;
	}
}
