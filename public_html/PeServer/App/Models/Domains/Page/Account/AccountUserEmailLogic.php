<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\Database;
use PeServer\Core\HttpStatus;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\AppMailer;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\Domains\AccountValidator;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Database\Domains\UserDomainDao;
use PeServer\App\Models\Database\Entities\UsersEntityDao;
use PeServer\App\Models\Database\Entities\UserChangeWaitEmailsEntityDao;

class AccountUserEmailLogic extends PageLogicBase
{
	/**
	 * Undocumented function
	 *
	 * @var array{email:string,wait_email:string,token_timestamp_utc:string}
	 */
	private array $defaultValues = [
		'email' => '',
		'wait_email' => '',
		'token_timestamp_utc' => '',
	];

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$database = $this->openDatabase();

		$userDomainDao = new UserDomainDao($database);
		$values = $userDomainDao->selectEmailAndWaitTokenTimestamp(
			$userInfo['user_id'],
			AppConfiguration::$json['config']['confirm']['user_change_wait_email_minutes']
		);

		if (!StringUtility::isNullOrWhiteSpace($values['email'])) {
			$this->defaultValues['email'] = AppCryptography::decrypt($values['email']);
		} else {
			$this->defaultValues['email'] = '';
		}
		if (!StringUtility::isNullOrWhiteSpace($values['wait_email'])) {
			$this->defaultValues['wait_email'] = AppCryptography::decrypt($values['wait_email']);
		} else {
			$this->defaultValues['wait_email'] = '';
		}

		$this->defaultValues['token_timestamp_utc'] = $values['token_timestamp_utc'];
	}

	protected function registerKeys(LogicCallMode $callMode): void
	{
		parent::registerParameterKeys([
			'account_email_email',
			'account_email_token',
			'wait_email',
			'token_timestamp_utc',
		], true);

		$this->setValue('account_email_token', '');
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
			'user_id' => $account['user_id'],
			'email' => AppCryptography::encrypt($email),
			'mark_email' => AppCryptography::toMark($email),
			'token' => sprintf('%08d', mt_rand(0, 99999999)),
		];

		$database = $this->openDatabase();

		$database->transaction(function (Database $database, array $params) {
			$userChangeWaitEmailsEntityDao = new UserChangeWaitEmailsEntityDao($database);

			$userChangeWaitEmailsEntityDao->deleteByUserId($params['user_id']);
			$userChangeWaitEmailsEntityDao->insertWaitEmails($params['user_id'], $params['email'], $params['mark_email'], $params['token']);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EMAIL_CHANGING, ['token' => $params['token']], $database);

			return true;
		}, $params);

		// トークン通知メール送信
		$subject = I18n::message('subject/email_change_token');
		$values = [
			'name' => $account['name'],
			'token' => $params['token'],
		];
		$html = AppTemplate::createMailTemplate('change-email-token', $subject, $values);

		$mailer = new AppMailer();
		$mailer->toAddresses = [
			['address' => $email, 'name' => $account['name']],
		];
		$mailer->subject = I18n::message('subject/email_change_token');
		$mailer->setMessage([
			'html' => $html,
		]);

		$mailer->send();
		//file_put_contents('X:\00_others\00_others\a.html',$html);
	}

	private function executeConfirm(LogicCallMode $callMode): void
	{
		$account = SessionManager::getAccount();

		$params = [
			'user_id' => $account['user_id'],
			'token' => $this->getRequest('account_email_token'),
		];

		$database = $this->openDatabase();
		$result = $database->transaction(function (Database $database, array $params) {
			$userDomainDao = new UserDomainDao($database);

			$this->logger->trace('あかんかぁ');
			$updated = $userDomainDao->updateEmailFromWaitEmail(
				$params['user_id'],
				$params['token'],
				AppConfiguration::$json['config']['confirm']['user_change_wait_email_minutes']
			);

			$this->logger->trace('ここまで来てんのかい');

			if (!$updated) {
				return false;
			}

			$userChangeWaitEmailsEntityDao = new UserChangeWaitEmailsEntityDao($database);
			$userChangeWaitEmailsEntityDao->deleteByUserId($params['user_id']);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EMAIL_CHANGED, ['token' => $params['token']], $database);

			return true;
		}, $params);

		if (!$result) {
			$this->addError('account_email_token', I18n::message('error/email_confirm_token_not_found'));
			return;
		}

		// 新旧メールアドレスにそれぞれ通知メール送信
		$items = [
			[
				'template' => 'change-email-new',
				'subject' => 'subject/email_change_new',
				'email' => $this->defaultValues['wait_email'],
			],
			[
				'template' => 'change-email-old',
				'subject' => 'subject/email_change_old',
				'email' => $this->defaultValues['email'],
			],
		];

		foreach ($items as $item) {
			$subject = I18n::message($item['subject']);
			$values = [
				'user_id' => $account['user_id'],
				'login_id' => $account['login_id'],
				'name' => $account['name'],
				'new_email' => $this->defaultValues['wait_email'],
				'old_email' => $this->defaultValues['email'],
			];
			$html = AppTemplate::createMailTemplate($item['template'], $subject, $values);

			$mailer = new AppMailer();
			$mailer->toAddresses = [
				['address' => $item['email'], 'name' => $account['name']],
			];
			$mailer->subject = $subject;
			$mailer->setMessage([
				'html' => $html,
			]);

			$mailer->send();
		}

		$this->result['confirm'] = true;
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
