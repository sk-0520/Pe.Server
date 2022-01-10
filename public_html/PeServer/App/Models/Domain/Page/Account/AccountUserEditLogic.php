<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Database\Database;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;

class AccountUserEditLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
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
		$userInfo = SessionManager::getAccount();

		$database = $this->openDatabase();
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
		$userInfo = SessionManager::getAccount();

		$params = [
			'user_id' => $userInfo['user_id'],
			'user_name' => $this->getRequest('account_edit_name'),
			'website' => $this->getRequest('account_edit_website'),
		];

		$database = $this->openDatabase();

		$database->transaction(function (IDatabaseContext $context, $params) {
			$usersEntityDao = new UsersEntityDao($context);

			// ユーザー情報更新
			$usersEntityDao->updateUserSetting(
				$params['user_id'],
				$params['user_name'],
				$params['website']
			);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EDIT, null, $context);

			return true;
		}, $params);

		$account = SessionManager::getAccount();
		$account['user_name'] = $params['user_name'];
		SessionManager::setAccount($account);

		$this->addTemporaryMessage(I18n::message('message/flash/updated_user'));
		AppDatabaseCache::exportUserInformation();
	}
}
