<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\Core\I18n;
use PeServer\Core\Database\Database;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Mvc\Validations;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Dao\Domains\UserDomainDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;

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
