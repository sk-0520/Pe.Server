<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\ManagementControl;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Stopwatch;

class ManagementControlUserListLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$database = $this->openDatabase();
		$userDomainDao = new UserDomainDao($database);
		$users = $userDomainDao->selectUserItems();

		$this->setValue('users', $users);
	}
}
