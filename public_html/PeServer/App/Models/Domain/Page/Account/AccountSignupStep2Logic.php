<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\App\Models\Domain\UserUtility;
use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Cryptography;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class AccountSignupStep2Logic extends PageLogicBase
{
	use SessionAnonymousTrait;

	public function __construct(LogicParameter $parameter, private AppCryptography $cryptography, private AppDatabaseCache $dbCache, private Mailer $mailer, private AppTemplate $appTemplate)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'token',
			'account_signup_email',
			'account_signup_login_id',
			'account_signup_password',
			'account_signup_password_confirm',
			'account_signup_name',
		], true);

		$this->setValue('account_signup_password', Text::EMPTY);
		$this->setValue('account_signup_password_confirm', Text::EMPTY);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->throwHttpStatusIfNotSignup2(HttpStatus::NotFound);

		$this->validation('account_signup_email', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);

			if ($accountValidator->isEmail($key, $value)) {
				$database = $this->openDatabase();
				$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($database);

				// ミドルウェアでトークン確認しているので取得できない場合は例外でOK
				$email = $signUpWaitEmailsEntityDao->selectEmail($this->getRequest('token'));
				$rawEmail = $this->cryptography->decrypt($email);
				if ($value !== $rawEmail) {
					$this->addError($key, I18n::message('error/sign_up_not_equal_email'));
				}
			}
		});

		$this->validation('account_signup_login_id', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			if ($accountValidator->isLoginId($key, $value)) {
				$database = $this->openDatabase();
				$accountValidator->isFreeLoginId($database, $key, $value);
			}
		});

		$this->validation('account_signup_password', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		}, ['trim' => false]);

		$this->validation('account_signup_password_confirm', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
			$newPassword = $this->getRequest('account_signup_password', Text::EMPTY, false);
			if ($value !== $newPassword) {
				$this->addError($key, I18n::message('error/password_confirm'));
			}
		});

		$this->validation('account_signup_name', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isUserName($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setSession(SessionKey::ANONYMOUS, new SessionAnonymous(signup2: true));
			return;
		}

		$userId = UserUtility::generateUserId();
		$token = $this->getRequest('token');
		$email = $this->getRequest('account_signup_email');
		$password = $this->getRequest('account_signup_password', Text::EMPTY, false);

		$params = [
			'token' => $token,
			'user_id' => $userId,
			'login_id' => $this->getRequest('account_signup_login_id'),
			'email' => $this->cryptography->encrypt($email),
			'mark_email' => $this->cryptography->toMark($email),
			'user_name' => $this->getRequest('account_signup_name'),
			'password' => Cryptography::toHashPassword($password)
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$usersEntityDao = new UsersEntityDao($context);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);
			$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($context);

			$usersEntityDao->insertUser(
				$params['user_id'],
				$params['login_id'],
				UserLevel::USER,
				UserState::ENABLED,
				$params['user_name'],
				$params['email'],
				$params['mark_email'],
				Text::EMPTY,
				Text::EMPTY,
				Text::EMPTY
			);

			$userAuthenticationsEntityDao->insertUserAuthentication(
				$params['user_id'],
				$params['password']
			);

			$signUpWaitEmailsEntityDao->deleteToken($params['token']);

			$this->writeAuditLogTargetUser($params['user_id'], AuditLog::USER_CREATE, ['token' => $params['token']], $context);

			return true;
		});

		$subject = I18n::message('subject/sign_up_step2');
		$values = [
			'name' => $params['user_name'],
			'login_id' => $params['login_id'],
		];
		$html = $this->appTemplate->createMailTemplate('mail_signup_step2', $subject, $values);

		$this->mailer->toAddresses = [
			new EmailAddress($email, $params['user_name']),
		];
		$this->mailer->subject = $subject;
		$this->mailer->setMessage(new EmailMessage(null, $html));

		$this->mailer->send();

		$this->removeSession(SessionKey::ANONYMOUS);
		$this->setSession(SessionKey::ACCOUNT, new SessionAccount(
			$userId,
			$params['login_id'],
			$params['user_name'],
			UserLevel::USER,
			UserState::ENABLED
		));
		$this->dbCache->exportUserInformation();
		$this->restartSession();

		$this->addTemporaryMessage('現在ログイン中です');
		$this->addTemporaryMessage('ユーザー登録が完了しました、通知メールを確認してください');
	}

	#endregion
}
