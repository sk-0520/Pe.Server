<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\Database;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use \PeServer\App\Models\AuditLog;
use \PeServer\App\Models\SessionKey;
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

	protected function startup(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$database = Database::open();
		$usersEntityDao = new UsersEntityDao($database);

		$userData = $usersEntityDao->selectUserEditData($userInfo['user_id']);

		$map = [
			'user_id' => 'account_user_id',
			'login_id' => 'account_user_login_id',
			'level' => 'account_user_level',
			'name' => 'account_user_name',
			'email' => 'account_edit_email',
			'website' => 'account_edit_website',
		];

		if ($callMode->isInitialize()) {
			foreach ($userData as $key => $value) {
				$this->setValue($map[$key], $value);
			}
		} else {
			$targets = [
				'user_id',
				'login_id',
				'level',
			];
			foreach ($userData as $key => $value) {
				if (ArrayUtility::contains($targets, $key)) {
					$this->setValue($map[$key], $value);
				} else {
					$this->setValue($map[$key], $this->getRequest($map[$key], ''));
				}
			}
		}
	}

	protected function registerKeys(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'account_user_name',
			'account_edit_email',
			'account_edit_website',
		], false, true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('account_user_name', function (string $key, ?string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isUserName($key, $value);
		});

		$this->validation('account_edit_email', function (string $key, ?string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isEmail($key, $value);
		});

		$this->validation('account_edit_website', function (string $key, ?string $value) {
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
	}

	private function executeSubmit(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$params = [
			'id' => $userInfo['user_id'],
			'user_name' => $this->getRequest('account_user_name'),
			'email' => $this->getRequest('account_edit_email'),
			'website' => $this->getRequest('account_edit_website'),
		];

		$database = Database::open();

		$result = $database->transaction(function ($database, $params) {
			$usersEntityDao = new UsersEntityDao($database);

			// 管理者ユーザーの登録
			$usersEntityDao->updateUserSetting(
				$params['id'],
				$params['user_name'],
				$params['email'],
				$params['website']
			);

			$this->writeAuditLogCurrentUser(AuditLog::USER_EDIT, null, $database);

			return true;
		}, $params);

		if (!$result) {
			$this->logger->error('未実装');
		}
	}
}
