<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\I18n;
use \PeServer\Core\Database;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Database\Domains\UserDomainDao;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;

class AccountLoginLogic extends LogicBase
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

		$user = $userDomainDao->selectUser([
			'account_login_login_id' => $this->getRequest('account_login_login_id'),
		]);

		if (is_null($user)) {
			$this->addError(Validations::COMMON, I18n::message('ID・パスワードが不明です'));
			return;
		}

		// パスワード突合
		$verify_ok = password_verify($this->getRequest('account_login_password'), $user['password']);
		if($verify_ok) {

		}
	}
}
