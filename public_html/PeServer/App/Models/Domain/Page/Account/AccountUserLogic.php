<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;

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
		$userInfo = SessionManager::getAccount();

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
