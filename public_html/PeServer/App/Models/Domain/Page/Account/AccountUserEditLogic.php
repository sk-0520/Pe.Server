<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class AccountUserEditLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'account_edit_name',
			'account_edit_website',
			'account_edit_description',
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

		$this->validation('account_edit_description', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isDescription($key, $value);
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
		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$database = $this->openDatabase();
		$usersEntityDao = new UsersEntityDao($database);

		$userEditData = $usersEntityDao->selectUserEditData($userInfo->userId);

		$map = [
			'name' => 'account_edit_name',
			'website' => 'account_edit_website',
			'description' => 'account_edit_description',
		];

		foreach ($userEditData->fields as $key => $value) {
			$this->setValue($map[$key], $value);
		}
	}

	private function executeSubmit(LogicCallMode $callMode): void
	{
		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$params = [
			'user_id' => $userInfo->userId,
			'user_name' => $this->getRequest('account_edit_name'),
			'website' => $this->getRequest('account_edit_website'),
			'description' => $this->getRequest('account_edit_description'),
		];

		$database = $this->openDatabase();

		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$usersEntityDao = new UsersEntityDao($context);

			// ユーザー情報更新
			$usersEntityDao->updateUserSetting(
				$params['user_id'],
				$params['user_name'],
				$params['website'],
				$params['description']
			);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EDIT, null, $context);

			return true;
		});


		$source = $this->requireSession(SessionKey::ACCOUNT);
		$account = new SessionAccount(
			$source->userId,
			$source->loginId,
			$params['user_name'],
			$source->level,
			$source->state
		);
		$this->setSession(SessionKey::ACCOUNT, $account);

		$this->addTemporaryMessage(I18n::message('message/flash/updated_user'));
		$this->dbCache->exportUserInformation();
	}

	#endregion
}
