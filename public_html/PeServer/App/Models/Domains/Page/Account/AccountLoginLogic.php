<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\I18n;
use \PeServer\Core\Database;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\App\Models\Database\Domains\UserDomainDao;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;

class AccountLoginLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$loginId = $this->getRequest('account_login_login_id');
		if (StringUtility::isNullOrWhiteSpace($loginId)) {
			$this->addError(Validations::COMMON, I18n::message('ID・パスワードが不明です'));
		}

		$password = $this->getRequest('account_login_password');
		if (StringUtility::isNullOrWhiteSpace($password)) {
			$this->addError(Validations::COMMON, I18n::message('ID・パスワードが不明です'));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$database = Database::open();

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
			$this->addError(Validations::COMMON, I18n::message('ID・パスワードが不明です'));
			return;
		}

		if ($existsSetupUser && $user['level'] !== 'setup') {
			$this->addError(Validations::COMMON, I18n::message('ID・パスワードが不明です'));
			$this->logger->error('未セットアップ状態での通常ログインは抑制中');
			return;
		}

		// パスワード突合
		$verify_ok = password_verify($this->getRequest('account_login_password'), $user['password']);
		if (!$verify_ok) {
			$this->addError(Validations::COMMON, I18n::message('ID・パスワードが不明です'));
			$this->logger->warn('ログイン失敗: {0}', $user['user_id']);
			return;
		}

		$this->removeSession(self::SESSION_ALL_CLEAR);
		$this->setSession('user', [
			'id' => $user['user_id'],
			'login_id' => $user['login_id'],
			'name' => $user['name'],
			'level' => $user['level'],
		]);
		$this->restartSession();
	}
}
