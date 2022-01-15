<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\Cryptography;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\App\Models\Domain\UserUtility;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;


class SettingSetupLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
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

		$this->validation('setting_setup_password', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		}, ['trim' => false]);

		$this->validation('setting_setup_user_name', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isUserName($key, $value);
		});

		$this->validation('setting_setup_email', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isEmail($key, $value);
		});

		$this->validation('setting_setup_website', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isWebsite($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$currentUserInfo = SessionManager::getAccount();

		$email = $this->getRequest('setting_setup_email');

		$params = [
			'login_id' => $this->getRequest('setting_setup_login_id'),
			'password' => $this->getRequest('setting_setup_password', '', false),
			'user_name' => $this->getRequest('setting_setup_user_name'),
			'email' => AppCryptography::encrypt($email),
			'mark_email' => AppCryptography::toMark($email),
			'website' => $this->getRequest('setting_setup_website'),
		];

		$userInfo = [
			'id' => UserUtility::generateUserId(),
			'generate_password' => '',
			'current_password' => Cryptography::toHashPassword($params['password']),
		];


		$database = $this->openDatabase();

		$result = $database->transaction(function (IDatabaseContext $database, $currentUserInfo, $params, $userInfo) {
			$accountValidator = new AccountValidator($this, $this->validator);

			if (!$accountValidator->isFreeLoginId($database, 'setting_setup_login_id', $params['login_id'])) {
				return false;
			}

			$usersEntityDao = new UsersEntityDao($database);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($database);

			// 管理者ユーザーの登録
			$usersEntityDao->insertUser(
				$userInfo['id'],
				$params['login_id'],
				UserLevel::ADMINISTRATOR,
				UserState::ENABLED,
				$params['user_name'],
				$params['email'],
				$params['mark_email'],
				$params['website'],
				'',
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
			$this->writeAuditLogCurrentUser(AuditLog::USER_STATE_CHANGE, ['state' => $state], $database);
			$this->writeAuditLogCurrentUser(AuditLog::USER_CREATE, ['user_id' => $userInfo['id']], $database);
			$this->writeAuditLogTargetUser($userInfo['id'], AuditLog::USER_GENERATED, ['user_id' => $currentUserInfo['user_id']], $database);

			return true;
		}, $currentUserInfo, $params, $userInfo);

		// 生成したのであれば現在のセットアップユーザーは用済みなのでログアウト
		if ($result) {
			$this->logger->info("セットアップユーザーお役目終了");
			$this->shutdownSession();
		}
	}
}
