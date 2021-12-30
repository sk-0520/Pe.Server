<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\Database;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use \PeServer\App\Models\AuditLog;
use \PeServer\App\Models\SessionManager;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\AccountValidator;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;

class AccountUserEditLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeys(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'account_edit_name',
			'account_edit_website',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('account_edit_name', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isUserName($key, $value);
		});

		$this->validation('account_edit_website', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isWebsite($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			$this->executeInitialize($callMode);
		} else {
			$this->executeSubmit($callMode);
		}
	}

	private function executeInitialize(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$database = Database::open();
		$usersEntityDao = new UsersEntityDao($database);

		$userEditData = $usersEntityDao->selectUserEditData($userInfo['user_id']);

		$map = [
			'name' => 'account_edit_name',
			'website' => 'account_edit_website',
		];

		foreach ($userEditData as $key => $value) {
			$this->setValue($map[$key], $value);
		}
	}

	private function executeSubmit(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$params = [
			'id_user' => $userInfo['user_id'],
			'user_name' => $this->getRequest('account_edit_name'),
			'website' => $this->getRequest('account_edit_website'),
		];

		$database = Database::open();

		$result = $database->transaction(function ($database, $params) {
			$usersEntityDao = new UsersEntityDao($database);

			// ユーザー情報更新
			$usersEntityDao->updateUserSetting(
				$params['id_user'],
				$params['user_name'],
				$params['website']
			);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EDIT, null, $database);

			return true;
		}, $params);

		if ($result) {
			$account = SessionManager::getAccount();
			$account['user_name'] = $params['user_name'];
			SessionManager::setAccount($account);
		}
	}
}
