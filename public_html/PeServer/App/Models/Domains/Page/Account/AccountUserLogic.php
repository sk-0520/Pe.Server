<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\App\Models\AppCryptography;
use PeServer\Core\I18n;
use PeServer\Core\Database\Database;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\Core\Mvc\Validator;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Dao\Domains\UserDomainDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;

class AccountUserLogic extends PageLogicBase
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
		$userInfo = $this->userInfo();

		$database = $this->openDatabase();

		$usersEntityDao = new UsersEntityDao($database);
		$pluginsEntityDao = new PluginsEntityDao($database);

		$userInfoData = $usersEntityDao->selectUserInfoData($userInfo['user_id']);
		$userPlugins = $pluginsEntityDao->selectPluginByUserId($userInfo['user_id']);

		if (!StringUtility::isNullOrWhiteSpace($userInfoData['email'])) {
			$userInfoData['email'] = AppCryptography::decrypt($userInfoData['email']);
		}

		$map = [
			'user_id' => 'account_user_id',
			'login_id' => 'account_user_login_id',
			'level' => 'account_user_level',
			'name' => 'account_user_name',
			'email' => 'account_user_email',
			'website' => 'account_user_website',
		];

		foreach ($userInfoData as $key => $value) {
			$this->setValue($map[$key], $value);
		}
		$this->setValue('plugins', $userPlugins);
	}
}
