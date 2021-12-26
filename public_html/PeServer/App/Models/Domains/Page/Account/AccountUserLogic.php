<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\I18n;
use \PeServer\Core\Database;
use \PeServer\Core\StringUtility;
use \PeServer\App\Models\AuditLog;
use \PeServer\Core\Mvc\Validator;
use \PeServer\App\Models\SessionKey;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\App\Models\Database\Domains\UserDomainDao;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;

class AccountUserLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeysImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->userInfo();

		$database = Database::open();
		$usersEntityDao = new UsersEntityDao($database);

		$userData = $usersEntityDao->selectUserEditData($userInfo['user_id']);

		$map = [
			'user_id' => 'account_user_id',
			'login_id' => 'account_user_login_id',
			'level' => 'account_user_level',
			'name' => 'account_user_name',
			'email' => 'account_edit_email',
			'website' => 'account_edit_website',
		];

		foreach ($userData as $key => $value) {
			$this->setValue($map[$key], $value);
		}
	}
}
