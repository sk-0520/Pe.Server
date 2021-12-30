<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
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
	 * @param array{email:string,wait_email:string,token_timestamp:string} $parameter
	 */
	private array $defaultValues = [
		'email' => '',
		'wait_email' => '',
		'token_timestamp' => '',
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
		$this->defaultValues = $userDomainDao->selectEmailAndWaitTokenTimestamp(
			$userInfo['user_id'],
			AppConfiguration::$json['confirm']['user_change_wait_email_minutes']
		);
	}

	protected function registerKeys(LogicCallMode $callMode): void
	{
		parent::registerParameterKeys([
			'account_email_email',
			'account_email_token',
			'wait_email',
			'token_timestamp'
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

				if (StringUtility::isNullOrWhiteSpace($this->defaultValues['token_timestamp'])) {
					$this->addError($key, I18n::message('error/email-confirm-token-not-found'));
				}
			});
		} else {
			$this->logger->warn('不明なモード要求 {0}', $mode);
			$this->addError(Validator::COMMON, I18n::message('error/unknown-email-mode'));
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
		$userInfo = $this->userInfo();
	}

	private function executeConfirm(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$this->result['confirm'] = true;
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		if (!($this->getRequest('account_email_mode') == 'edit' && $callMode->isSubmit())) {
			$this->setValue('account_email_email', $this->defaultValues['email']);
		}

		$this->setValue('wait_email', $this->defaultValues['wait_email']);
		$this->setValue('token_timestamp', $this->defaultValues['token_timestamp']);
	}
}
