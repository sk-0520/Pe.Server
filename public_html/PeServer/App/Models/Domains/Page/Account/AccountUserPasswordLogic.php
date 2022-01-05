<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Cryptography;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Database\Database;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domains\AccountValidator;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;

class AccountUserPasswordLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'account_password_current',
			'account_password_new',
			'account_password_confirm',
		], false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('account_password_current', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);

			$database = $this->openDatabase();
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($database);
			$passwords = $userAuthenticationsEntityDao->selectPasswords($this->userInfo()['user_id']);

			if (!Cryptography::verifyPassword($value, $passwords['current_password'])) {
				$this->addError($key, I18n::message('error/password_incorrect'));
			}
			if (!StringUtility::isNullOrEmpty($passwords['generate_password'])) {
				if (!Cryptography::verifyPassword($value, $passwords['generate_password'])) {
					$this->addError($key, I18n::message('error/password_generate'));
				}
			}
		});

		$this->validation('account_password_new', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		}, ['trim' => false]);

		$this->validation('account_password_confirm', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
			$newPassword = $this->getRequest('account_password_new', '', false);
			if ($value !== $newPassword) {
				$this->addError($key, I18n::message('error/password_confirm'));
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$userInfo = $this->userInfo();

		$newPassword = $this->getRequest('account_password_new', '', false);

		$params = [
			'user_id' => $userInfo['user_id'],
			'password' => Cryptography::toHashPassword($newPassword),
		];

		$database = $this->openDatabase();

		$database->transaction(function (IDatabaseContext $context, $params) {
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);
			$userAuthenticationsEntityDao->updateCurrentPassword($params['user_id'], $params['password']);

			$this->writeAuditLogCurrentUser(AuditLog::USER_PASSWORD_CHANGE, null, $context);

			return true;
		}, $params);


		$this->addTemporaryMessage(I18n::message('message/flash/updated_password'));
	}
}
