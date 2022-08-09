<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\Cryptography;
use PeServer\Core\DefaultValue;
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
use PeServer\Core\ArrayUtility;

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

		$this->setValue('setting_setup_password', DefaultValue::EMPTY_STRING);
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
			'password' => $this->getRequest('setting_setup_password', DefaultValue::EMPTY_STRING, false),
			'user_name' => $this->getRequest('setting_setup_user_name'),
			'email' => AppCryptography::encrypt($email),
			'mark_email' => AppCryptography::toMark($email),
			'website' => $this->getRequest('setting_setup_website'),
		];

		$userInfo = [
			'id' => UserUtility::generateUserId(),
			'generated_password' => DefaultValue::EMPTY_STRING,
			'current_password' => Cryptography::toHashPassword($params['password']),
		];


		$database = $this->openDatabase();

		$result = $database->transaction(function (IDatabaseContext $database) use ($currentUserInfo, $params, $userInfo) {
			$accountValidator = new AccountValidator($this, $this->validator);

			/** @var string @-phpstan-ignore-next-line */
			$loginId = ArrayUtility::getOr($params, 'login_id', '');
			if (!$accountValidator->isFreeLoginId($database, 'setting_setup_login_id', $loginId)) {
				return false;
			}

			$usersEntityDao = new UsersEntityDao($database);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($database);

			// 管理者ユーザーの登録
			$usersEntityDao->insertUser(
				$userInfo['id'], // @-phpstan-ignore-line
				$loginId,
				UserLevel::ADMINISTRATOR,
				UserState::ENABLED,
				$params['user_name'], // @-phpstan-ignore-line
				$params['email'], // @-phpstan-ignore-line
				$params['mark_email'], // @-phpstan-ignore-line
				$params['website'], // @-phpstan-ignore-line
				DefaultValue::EMPTY_STRING,
				DefaultValue::EMPTY_STRING
			);

			$userAuthenticationsEntityDao->insertUserAuthentication(
				$userInfo['id'], // @-phpstan-ignore-line
				$userInfo['generated_password'], // @-phpstan-ignore-line
				$userInfo['current_password'] // @-phpstan-ignore-line
			);

			// 現在のセットアップユーザーを無効化
			$state = UserState::DISABLED;
			$usersEntityDao->updateUserState(
				$currentUserInfo->userId, // @-phpstan-ignore-line
				$state
			);

			// ユーザー生成記録を監査ログに追加
			$this->writeAuditLogCurrentUser(AuditLog::USER_STATE_CHANGE, ['state' => $state], $database);
			// @-phpstan-ignore-next-line
			$this->writeAuditLogCurrentUser(AuditLog::USER_CREATE, ['user_id' => $userInfo['id']], $database);
			// @-phpstan-ignore-next-line
			$this->writeAuditLogTargetUser($userInfo['id'], AuditLog::USER_GENERATED, ['user_id' => $currentUserInfo->userId], $database);

			return true;
		});

		// 生成したのであれば現在のセットアップユーザーは用済みなのでログアウト
		if ($result) {
			$this->logger->info("セットアップユーザーお役目終了");
			$this->shutdownSession();
		}
	}
}
