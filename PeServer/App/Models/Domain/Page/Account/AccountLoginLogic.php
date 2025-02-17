<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Cryptography;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Text;

class AccountLoginLogic extends PageLogicBase
{
	use SessionAnonymousTrait;

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

		$this->throwHttpStatusIfNotLogin(HttpStatus::NotFound);

		$loginId = $this->getRequest('account_login_login_id');
		if (Text::isNullOrWhiteSpace($loginId)) {
			$this->addCommonError(I18n::message(self::ERROR_LOGIN_PARAMETER));
		}

		$password = $this->getRequest('account_login_password');
		if (Text::isNullOrWhiteSpace($password)) {
			$this->addCommonError(I18n::message(self::ERROR_LOGIN_PARAMETER));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setSession(SessionKey::ANONYMOUS, new SessionAnonymous(login: true));
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
			$this->addCommonError(I18n::message(self::ERROR_LOGIN_PARAMETER));
			return;
		}

		if ($existsSetupUser && $user->level !== UserLevel::SETUP) {
			$this->addCommonError(I18n::message(self::ERROR_LOGIN_PARAMETER));
			$this->logger->error('未セットアップ状態での通常ログインは抑制中');
			return;
		}

		$loginRawPassword = $this->getRequest('account_login_password');
		// パスワード突合
		$verifyOk = Cryptography::verifyPassword($loginRawPassword, $user->currentPassword);
		if (!$verifyOk) {
			$this->addCommonError(I18n::message(self::ERROR_LOGIN_PARAMETER));
			$this->logger->warn('ログイン失敗: {0}', $user->userId);
			$this->writeAuditLogTargetUser($user->userId, AuditLog::LOGIN_FAILED);
			return;
		}
		// パスワードのアルゴリズムが古い場合に再設定する(業務ロジックのポリシー云々ではない)
		$isNeedRehashPassword = Cryptography::needsRehashPassword($user->currentPassword);
		if ($isNeedRehashPassword) {
			$info = Cryptography::getPasswordInformation($user->currentPassword);
			$this->logger->info("[OLD] password needs rehash: {0}, {1}", $user->userId, $info);
			$loginNewPassword = Cryptography::hashPassword($loginRawPassword);
			$database->transaction(function ($context) use ($user, $loginNewPassword) {
				$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);
				$userAuthenticationsEntityDao->updatePasswordOnly($user->userId, $loginNewPassword);
				$info = Cryptography::getPasswordInformation($loginNewPassword);
				$this->logger->info("[NEW] password needs rehash: {0}, {1}", $user->userId, $info);
				return true;
			});
		}

		$this->removeSession(self::SESSION_ALL_CLEAR);
		$account = new SessionAccount(
			$user->userId,
			$user->loginId,
			$user->name,
			$user->level,
			$user->state
		);
		$this->setSession(SessionKey::ACCOUNT, $account);
		$this->restartSession();
		$this->writeAuditLogCurrentUser(AuditLog::LOGIN_SUCCESS, $account);

		$userAuthenticationsEntityDao->updateClearReminder($account->userId);
	}

	#endregion
}
