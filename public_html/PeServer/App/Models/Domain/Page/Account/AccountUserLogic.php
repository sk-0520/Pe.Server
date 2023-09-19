<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\Core\Text;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionKey;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\Core\Collections\Arr;

class AccountUserLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$database = $this->openDatabase();

		$usersEntityDao = new UsersEntityDao($database);
		$pluginsEntityDao = new PluginsEntityDao($database);

		$userInfoData = $usersEntityDao->selectUserInfoData($userInfo->userId);
		$userPlugins = $pluginsEntityDao->selectPluginByUserId($userInfo->userId);

		$map = [
			'user_id' => 'account_user_id',
			'login_id' => 'account_user_login_id',
			'level' => 'account_user_level',
			'name' => 'account_user_name',
			'website' => 'account_user_website',
		];

		foreach ($userInfoData->fields as $key => $value) {
			if (Arr::containsKey($map, $key)) {
				$this->setValue($map[$key], $value);
			}
		}
		$this->setValue('plugins', $userPlugins->rows);
	}

	#endregion
}
