<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\Database\Database;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Mvc\Validator;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Dao\Domains\UserDomainDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\Core\Cryptography;

class AccountLoginLogic extends PageLogicBase
{
	private const ERROR_LOGIN_PARAMETER = 'error/login_parameter';

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'account_login_login_id',
			'account_login_password',
		], false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$loginId = $this->getRequest('account_login_login_id');
		if (StringUtility::isNullOrWhiteSpace($loginId)) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
		}

		$password = $this->getRequest('account_login_password');
		if (StringUtility::isNullOrWhiteSpace($password)) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$database = $this->openDatabase();

		$usersEntityDao = new UsersEntityDao($database);
		$userDomainDao = new UserDomainDao($database);

		$existsSetupUser = $usersEntityDao->selectExistsSetupUser();
		if ($existsSetupUser) {
			$this->logger->info('セットアップ ユーザー 検証');
		} else {
			$this->logger->info('通常 ユーザー 検証');
		}

		$user = $userDomainDao->selectLoginUser($this->getRequest('account_login_login_id'));

		if (is_null($user)) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
			return;
		}

		if ($existsSetupUser && $user['level'] !== 'setup') {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
			$this->logger->error('未セットアップ状態での通常ログインは抑制中');
			return;
		}

		// パスワード突合
		$verify_ok = Cryptography::verifyPassword($this->getRequest('account_login_password'), $user['current_password']);
		if (!$verify_ok) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
			$this->logger->warn('ログイン失敗: {0}', $user['user_id']);
			$this->writeAuditLogTargetUser($user['user_id'], AuditLog::LOGIN_FAILED);
			return;
		}

		$this->removeSession(self::SESSION_ALL_CLEAR);
		$account = [
			'user_id' => $user['user_id'],
			'login_id' => $user['login_id'],
			'name' => $user['name'],
			'level' => $user['level'],
			'state' => $user['state'],
		];
		SessionManager::setAccount($account);
		$this->restartSession();
		$this->writeAuditLogCurrentUser(AuditLog::LOGIN_SUCCESS, $account);
	}
}
