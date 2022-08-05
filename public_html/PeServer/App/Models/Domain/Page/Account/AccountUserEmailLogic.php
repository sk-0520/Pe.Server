<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UserChangeWaitEmailsEntityDao;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;

class AccountUserEmailLogic extends PageLogicBase
{
	/**
	 * Undocumented function
	 *
	 * @var array{email:string,wait_email:string,token_timestamp_utc:string}
	 */
	private array $defaultValues = [
		'email' => InitialValue::EMPTY_STRING,
		'wait_email' => InitialValue::EMPTY_STRING,
		'token_timestamp_utc' => InitialValue::EMPTY_STRING,
	];

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$userInfo = SessionManager::getAccount();

		$database = $this->openDatabase();

		$userDomainDao = new UserDomainDao($database);
		$values = $userDomainDao->selectEmailAndWaitTokenTimestamp(
			$userInfo->userId,
			AppConfiguration::$config['config']['confirm']['user_change_wait_email_minutes']
		);

		if (!StringUtility::isNullOrWhiteSpace($values->fields['email'])) {
			$this->defaultValues['email'] = AppCryptography::decrypt($values->fields['email']);
		} else {
			$this->defaultValues['email'] = InitialValue::EMPTY_STRING;
		}
		if (!StringUtility::isNullOrWhiteSpace($values->fields['wait_email'])) {
			$this->defaultValues['wait_email'] = AppCryptography::decrypt($values->fields['wait_email']);
		} else {
			$this->defaultValues['wait_email'] = InitialValue::EMPTY_STRING;
		}

		$this->defaultValues['token_timestamp_utc'] = $values->fields['token_timestamp_utc'];

		parent::registerParameterKeys([
			'account_email_email',
			'account_email_token',
			'wait_email',
			'token_timestamp_utc',
		], true);

		$this->setValue('account_email_token', InitialValue::EMPTY_STRING);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$mode = $this->getRequest('account_email_mode');
		if ($mode === 'edit') {
			$this->validation('account_email_email', function (string $key, string $value) {
				$accountValidator = new AccountValidator($this, $this->validator);
				$accountValidator->isEmail($key, $value);
			});
		} else if ($mode === 'confirm') {
			$this->validation('account_email_token', function (string $key, string $value) {
				$this->validator->isNotWhiteSpace($key, $value);

				if (StringUtility::isNullOrWhiteSpace($this->defaultValues['token_timestamp_utc'])) {
					$this->addError($key, I18n::message('error/email_confirm_token_not_found'));
				}
			});
		} else {
			$this->logger->warn('不明なモード要求 {0}', $mode);
			$this->addError(Validator::COMMON, I18n::message('error/unknown_email_mode'));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$mode = $this->getRequest('account_email_mode');

		if ($mode === 'edit') {
			$this->executeEdit($callMode);
		} else {
			if ($mode !== 'confirm') {
				throw new NotImplementedException();
			}

			$this->executeConfirm($callMode);
		}
	}

	private function executeEdit(LogicCallMode $callMode): void
	{
		$account = SessionManager::getAccount();

		$email = $this->getRequest('account_email_email');

		$params = [
			'user_id' => $account->userId,
			'email' => AppCryptography::encrypt($email),
			'mark_email' => AppCryptography::toMark($email),
			'token' => sprintf('%08d', mt_rand(0, 99999999)),
		];

		$database = $this->openDatabase();

		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$userChangeWaitEmailsEntityDao = new UserChangeWaitEmailsEntityDao($context);

			$userChangeWaitEmailsEntityDao->deleteByUserId($params['user_id']);
			$userChangeWaitEmailsEntityDao->insertWaitEmails($params['user_id'], $params['email'], $params['mark_email'], $params['token']);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EMAIL_CHANGING, ['token' => $params['token']], $context);

			return true;
		});

		// トークン通知メール送信
		$subject = I18n::message('subject/email_change_token');
		$values = [
			'name' => $account->name,
			'token' => $params['token'],
		];
		$html = AppTemplate::createMailTemplate('change_email_token', $subject, $values);

		$mailer = new AppMailer();
		$mailer->toAddresses = [
			new EmailAddress($email, $account->name),
		];
		$mailer->subject = $subject;
		$mailer->setMessage(new EmailMessage(null, $html));

		$mailer->send();
		//file_put_contents('X:\00_others\00_others\a.html',$html);
		$this->addTemporaryMessage(I18n::message('message/flash/send_email_token'));
	}

	private function executeConfirm(LogicCallMode $callMode): void
	{
		$account = SessionManager::getAccount();

		$params = [
			'user_id' => $account->userId,
			'token' => $this->getRequest('account_email_token'),
		];

		$database = $this->openDatabase();
		$result = $database->transaction(function (IDatabaseContext $context) use ($params) {
			$userDomainDao = new UserDomainDao($context);
			$userChangeWaitEmailsEntityDao = new UserChangeWaitEmailsEntityDao($context);

			$existsToken = $userChangeWaitEmailsEntityDao->selectExistsToken(
				$params['user_id'],
				$params['token'],
				AppConfiguration::$config['config']['confirm']['user_change_wait_email_minutes']
			);

			if (!$existsToken) {
				return false;
			}

			$updated = $userDomainDao->updateEmailFromWaitEmail(
				$params['user_id'],
				$params['token']
			);

			if (!$updated) {
				return false;
			}

			$userChangeWaitEmailsEntityDao->deleteByUserId($params['user_id']);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EMAIL_CHANGED, ['token' => $params['token']], $context);

			return true;
		});

		if (!$result) {
			$this->addError('account_email_token', I18n::message('error/email_confirm_token_not_found'));
			return;
		}

		// 新旧メールアドレスにそれぞれ通知メール送信
		$items = [
			[
				'template' => 'change_email_new',
				'subject' => 'subject/email_change_new',
				'email' => $this->defaultValues['wait_email'],
			],
			[
				'template' => 'change_email_old',
				'subject' => 'subject/email_change_old',
				'email' => $this->defaultValues['email'],
			],
		];

		foreach ($items as $item) {
			$subject = I18n::message($item['subject']);
			$values = [
				'user_id' => $account->userId,
				'login_id' => $account->loginId,
				'name' => $account->name,
				'new_email' => $this->defaultValues['wait_email'],
				'old_email' => $this->defaultValues['email'],
			];
			$html = AppTemplate::createMailTemplate($item['template'], $subject, $values);

			$mailer = new AppMailer();
			$mailer->toAddresses = [
				new EmailAddress($item['email'], $account->name),
			];
			$mailer->subject = $subject;
			$mailer->setMessage(new EmailMessage(null, $html));

			$mailer->send();
		}

		$this->result['confirm'] = true;

		$this->addTemporaryMessage(I18n::message('message/flash/updated_email'));
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		if (!($this->getRequest('account_email_mode') == 'edit' && $callMode->isSubmit())) {
			$this->setValue('account_email_email', $this->defaultValues['email']);
		}

		$this->setValue('wait_email', $this->defaultValues['wait_email']);
		$this->setValue('token_timestamp_utc', $this->defaultValues['token_timestamp_utc']);
	}
}
