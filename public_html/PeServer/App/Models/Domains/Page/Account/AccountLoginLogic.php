<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

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

		$loginId = $this->getRequest('account-login-loginid');
		if (StringUtility::isNullOrWhiteSpace($loginId)) {
			$this->addError(Validations::COMMON, I18n::message('パスワード・パスワードが不明です'));
		}

		$password = $this->getRequest('account-login-password');
		if (StringUtility::isNullOrWhiteSpace($password)) {
			$this->addError(Validations::COMMON, I18n::message('パスワード・パスワードが不明です'));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		//NONE
	}
}
