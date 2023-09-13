<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Entities\UserChangeWaitEmailsEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\I18n;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

class AccountUserEmailLogic extends PageLogicBase
{
	/**
	 * Undocumented function
	 *
	 * @var array{email:string,wait_email:string,token_timestamp_utc:string}
	 */
	private array $defaultValues = [
		'email' => Text::EMPTY,
		'wait_email' => Text::EMPTY,
		'token_timestamp_utc' => Text::EMPTY,
	];

	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppCryptography $cryptography, private Mailer $mailer, private AppTemplate $appTemplate)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$database = $this->openDatabase();

		$userDomainDao = new UserDomainDao($database);
		$values = $userDomainDao->selectEmailAndWaitTokenTimestamp(
			$userInfo->userId,
			$this->config->setting->config->confirm->userChangeWaitEmailMinutes
		);

		if (!Text::isNullOrWhiteSpace($values->fields['email'])) {
			$this->defaultValues['email'] = $this->cryptography->decrypt($values->fields['email']);
		} else {
			$this->defaultValues['email'] = Text::EMPTY;
		}
		if (!Text::isNullOrWhiteSpace($values->fields['wait_email'])) {
			$this->defaultValues['wait_email'] = $this->cryptography->decrypt($values->fields['wait_email']);
		} else {
			$this->defaultValues['wait_email'] = Text::EMPTY;
		}

		$this->defaultValues['token_timestamp_utc'] = $values->fields['token_timestamp_utc'];

		parent::registerParameterKeys([
			'account_email_email',
			'account_email_token',
			'wait_email',
			'token_timestamp_utc',
		], true);

		$this->setValue('account_email_token', Text::EMPTY);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
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

				if (Text::isNullOrWhiteSpace($this->defaultValues['token_timestamp_utc'])) {
					$this->addError($key, I18n::message('error/email_confirm_token_not_found'));
				}
			});
		} else {
			$this->logger->warn('不明なモード要求 {0}', $mode);
			$this->addCommonError(I18n::message('error/unknown_email_mode'));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
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
		$account = $this->requireSession(SessionKey::ACCOUNT);

		$email = $this->getRequest('account_email_email');

		$params = [
			'user_id' => $account->userId,
			'email' => $this->cryptography->encrypt($email),
			'mark_email' => $this->cryptography->toMark($email),
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
		$html = $this->appTemplate->createMailTemplate('change_email_token', $subject, $values);

		$this->mailer->toAddresses = [
			new EmailAddress($email, $account->name),
		];
		$this->mailer->subject = $subject;
		$this->mailer->setMessage(new EmailMessage(null, $html));

		$this->mailer->send();
		//file_put_contents('X:\00_others\00_others\a.html',$html);
		$this->addTemporaryMessage(I18n::message('message/flash/send_email_token'));
	}

	private function executeConfirm(LogicCallMode $callMode): void
	{
		$account = $this->requireSession(SessionKey::ACCOUNT);

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
				$this->config->setting->config->confirm->userChangeWaitEmailMinutes
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
			$html = $this->appTemplate->createMailTemplate($item['template'], $subject, $values);

			$this->mailer->toAddresses = [
				new EmailAddress($item['email'], $account->name),
			];
			$this->mailer->subject = $subject;
			$this->mailer->setMessage(new EmailMessage(null, $html));

			$this->mailer->send();
		}

		$this->result['confirm'] = true;

		$this->addTemporaryMessage(I18n::message('message/flash/updated_email'));
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		if (!($this->getRequest('account_email_mode') == 'edit' && $callMode === LogicCallMode::Submit)) {
			$this->setValue('account_email_email', $this->defaultValues['email']);
		}

		$this->setValue('wait_email', $this->defaultValues['wait_email']);
		$this->setValue('token_timestamp_utc', $this->defaultValues['token_timestamp_utc']);
	}

	#endregion
}
