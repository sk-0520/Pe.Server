<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AuditLog;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\Domain\Page\PageLogicBase;

class AccountLogoutLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->getSession(SessionManager::ACCOUNT, null);
		if (is_null($userInfo)) {
			return;
		}

		$this->writeAuditLogCurrentUser(AuditLog::LOGOUT);

		$this->shutdownSession();
	}
}
