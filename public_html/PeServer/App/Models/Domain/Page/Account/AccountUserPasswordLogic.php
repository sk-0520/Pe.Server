<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\Cryptography;
use PeServer\Core\DefaultValue;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
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

			$userInfo = SessionManager::getAccount();

			$database = $this->openDatabase();
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($database);
			$passwords = $userAuthenticationsEntityDao->selectPasswords($userInfo->userId);

			if (!Cryptography::verifyPassword($value, $passwords->fields['current_password'])) {
				$this->addError($key, I18n::message('error/password_incorrect'));
			}
			if (!StringUtility::isNullOrEmpty($passwords->fields['generated_password'])) {
				if (!Cryptography::verifyPassword($value, $passwords->fields['generated_password'])) {
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
			$newPassword = $this->getRequest('account_password_new', DefaultValue::EMPTY_STRING, false);
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

		$userInfo = SessionManager::getAccount();

		$newPassword = $this->getRequest('account_password_new', DefaultValue::EMPTY_STRING, false);

		$params = [
			'user_id' => $userInfo->userId,
			'password' => Cryptography::toHashPassword($newPassword),
		];

		$database = $this->openDatabase();

		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);
			$userAuthenticationsEntityDao->updateCurrentPassword($params['user_id'], $params['password']);

			$this->writeAuditLogCurrentUser(AuditLog::USER_PASSWORD_CHANGE, null, $context);

			return true;
		});


		$this->addTemporaryMessage(I18n::message('message/flash/updated_password'));
	}
}
