<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Setting;

use \PeServer\Core\Uuid;
use \PeServer\Core\I18n;
use \PeServer\Core\Database;
use \PeServer\Core\StringUtility;
use \PeServer\App\Models\AuditLog;
use \PeServer\App\Models\UserLevel;
use \PeServer\App\Models\UserState;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\AccountValidator;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;
use \PeServer\App\Models\Database\Entities\UserAuthenticationsEntityDao;
use PeServer\Core\Throws\InvalidOperationException;

class SettingSetupLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeysImpl(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'setting_setup_login_id',
			'setting_setup_password',
			'setting_setup_user_name',
			'setting_setup_email',
			'setting_setup_website',
		], true);

		$this->setValue('setting_setup_password', '');
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('setting_setup_login_id', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isLoginId($key, $value);
		});

		$this->validation('setting_setup_password', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		});

		$this->validation('setting_setup_user_name', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isUserName($key, $value);
		});

		$this->validation('setting_setup_email', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isEmail($key, $value);
		});

		$this->validation('setting_setup_website', function ($key, $value) {
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
		$currentUserInfo = $this->userInfo();

		$params = [
			'login_id' => StringUtility::trim((string)$this->getRequest('setting_setup_login_id')),
			'password' => (string)$this->getRequest('setting_setup_password'),
			'user_name' => StringUtility::trim((string)$this->getRequest('setting_setup_user_name')),
			'email' => StringUtility::trim((string)$this->getRequest('setting_setup_email')),
			'website' => StringUtility::trim((string)$this->getRequest('setting_setup_website')),
		];

		$userInfo = [
			'id' => Uuid::generateGuid(),
			'generate_password' => '',
			'current_password' => password_hash($params['password'], PASSWORD_DEFAULT),
		];


		$database = Database::open();

		$result = $database->transaction(function ($db, $currentUserInfo, $params, $userInfo) {
			$accountValidator = new AccountValidator($this, $this->validator);

			if (!$accountValidator->isFreeLoginId($db, 'setting_setup_login_id', $params['login_id'])) {
				return false;
			}

			$usersEntityDao = new UsersEntityDao($db);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($db);

			// 管理者ユーザーの登録
			$usersEntityDao->insertUser(
				$userInfo['id'],
				$params['login_id'],
				UserLevel::ADMINISTRATOR,
				UserState::ENABLED,
				$params['user_name'],
				$params['email'],
				$params['website'],
				''
			);

			$userAuthenticationsEntityDao->insertUserAuthentication(
				$userInfo['id'],
				$userInfo['generate_password'],
				$userInfo['current_password']
			);

			// 現在のセットアップユーザーを無効化
			$state = UserState::DISABLED;
			$usersEntityDao->updateUserState(
				$currentUserInfo['user_id'],
				$state
			);

			// ユーザー生成記録を監査ログに追加
			$this->writeAuditLogCurrentUser(AuditLog::USER_STATE_CHANGE, $state, $db);
			$this->writeAuditLogCurrentUser(AuditLog::USER_CREATE, $userInfo['id'], $db);
			$this->writeAuditLogTargetUser($userInfo['id'], AuditLog::USER_GENERATED, null, $db);

			return true;
		}, $currentUserInfo, $params, $userInfo);

		// 生成したのであれば現在のセットアップユーザーは用済みなのでログアウト
		if ($result) {
			$this->logger->info("セットアップユーザーお役目終了");
			$this->shutdownSession();
		}
	}
}
