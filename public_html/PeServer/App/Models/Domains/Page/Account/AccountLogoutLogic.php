<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\I18n;
use \PeServer\Core\Database;
use \PeServer\Core\StringUtility;
use \PeServer\App\Models\AuditLog;
use \PeServer\Core\Mvc\Validations;
use \PeServer\App\Models\SessionKey;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\App\Models\Database\Domains\UserDomainDao;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;

class AccountLogoutLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeysImpl(LogicCallMode $callMode)
	{
		$this->registerParameterKeys([
			'',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->getSession(SessionKey::ACCOUNT, null);
		if(is_null($userInfo)) {
			return;
		}

		$this->writeAuditLog(AuditLog::LOGOUT);

		$this->shutdownSession();
	}
}
