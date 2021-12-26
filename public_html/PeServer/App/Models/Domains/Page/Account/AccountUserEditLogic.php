<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\Database;
use \PeServer\App\Models\AuditLog;
use \PeServer\App\Models\SessionKey;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\App\Models\Database\Entities\UsersEntityDao;

class AccountUserEditLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
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

		// @phpstan-ignore-next-line
		if ($callMode->initialize()) {
			foreach ($userData as $key => $value) {
				$this->setValue($map[$key], $value);
			}
		} else {
			$targets = [
				'user_id',
				'login_id',
				'level',
			];
			foreach ($userData as $key => $value) {
				if (array_search($key, $targets) !== false) {
					$this->setValue($map[$key], $value);
				}
			}
		}
	}

	protected function registerKeys(LogicCallMode $callMode): void
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
		if ($callMode->isInitialize()) {
			$this->executeInitialize($callMode);
		} else {
			$this->executeSubmit($callMode);
		}
	}

	private function executeInitialize(LogicCallMode $callMode): void
	{
	}

	private function executeSubmit(LogicCallMode $callMode): void
	{
	}
}
