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
		$this->logger->info('10');
		$userInfo = $this->userInfo();

		$this->logger->info('20');
		$database = Database::open();

		$this->logger->info('30');
		$usersEntityDao = new UsersEntityDao($database);

		$this->logger->info('40');
		$userData = $usersEntityDao->selectUserEditData($userInfo['user_id']);

		$this->logger->info('50');
		$map = [
			'user_id' => 'account_user_id',
			'login_id' => 'account_user_login_id',
			'level' => 'account_user_level',
			'name' => 'account_user_name',
			'email' => 'account_edit_email',
			'website' => 'account_edit_website',
		];

		$this->logger->info('60');
		foreach ($userData as $key => $value) {
			$this->setValue($map[$key], $value);
		}
	}
}
