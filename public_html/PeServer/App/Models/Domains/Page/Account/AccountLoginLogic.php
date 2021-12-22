<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\Database;
use \PeServer\Core\I18n;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\StringUtility;

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

		$setupCount = $database->queryFirst(
			<<<SQL

			select
				COUNT(*) as count
			from
				users
			where
				users.level = 'setup'
				and
				users.state = 'enabled'
SQL
		)['count'];

		$user = $database->queryFirstOrDefault(
			[
				'user_id' => '',
				'level' => '',
				'password' => '',
			],
			<<<SQL

			select
				users.user_id,
				users.level,
				user_authentications.current_password
			from
				users
				inner join
					user_authentications
					on
					(
						user_authentications.user_id = users.user_id
					)
			where
				users.login_id = :account_login_login_id
SQL
			/* AUTO FORMAT */,
			[
				'account_login_login_id' => $this->getRequest('account_login_login_id'),
			]
		);
	}
}
