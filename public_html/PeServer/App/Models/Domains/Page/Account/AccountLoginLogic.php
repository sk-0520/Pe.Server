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
		);

		if (0 < $setupCount['count']) {
			$this->addError(Validations::COMMON, I18n::message('初期化が完了していません'));
			return;
		}
	}
}
