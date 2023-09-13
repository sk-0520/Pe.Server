<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Cryptography;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Text;

class AccountLoginLogic extends PageLogicBase
{
	#region define

	private const ERROR_LOGIN_PARAMETER = 'error/login_parameter';

	#endregion

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'account_login_login_id',
			'account_login_password',
		], false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$loginId = $this->getRequest('account_login_login_id');
		if (Text::isNullOrWhiteSpace($loginId)) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
		}

		$password = $this->getRequest('account_login_password');
		if (Text::isNullOrWhiteSpace($password)) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$database = $this->openDatabase();

		$usersEntityDao = new UsersEntityDao($database);
		$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($database);
		$userDomainDao = new UserDomainDao($database);

		$existsSetupUser = $usersEntityDao->selectExistsSetupUser();
		if ($existsSetupUser) {
			$this->logger->info('セットアップ ユーザー 検証');
		} else {
			$this->logger->info('通常 ユーザー 検証');
		}

		$user = $userDomainDao->selectLoginUser($this->getRequest('account_login_login_id'));

		if ($user === null) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
			return;
		}

		if ($existsSetupUser && $user->fields['level'] !== UserLevel::SETUP) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
			$this->logger->error('未セットアップ状態での通常ログインは抑制中');
			return;
		}

		// パスワード突合
		$verifyOk = Cryptography::verifyPassword($this->getRequest('account_login_password'), $user->fields['current_password']);
		if (!$verifyOk) {
			$this->addError(Validator::COMMON, I18n::message(self::ERROR_LOGIN_PARAMETER));
			$this->logger->warn('ログイン失敗: {0}', $user->fields['user_id']);
			$this->writeAuditLogTargetUser($user->fields['user_id'], AuditLog::LOGIN_FAILED);
			return;
		}

		$this->removeSession(self::SESSION_ALL_CLEAR);
		$account = new SessionAccount(
			$user->fields['user_id'],
			$user->fields['login_id'],
			$user->fields['name'],
			$user->fields['level'],
			$user->fields['state']
		);
		$this->setSession(SessionKey::ACCOUNT, $account);
		$this->restartSession();
		$this->writeAuditLogCurrentUser(AuditLog::LOGIN_SUCCESS, $account);

		$userAuthenticationsEntityDao->updateClearReminder($account->userId);
	}

	#endregion
}
