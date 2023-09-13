<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Password;

use PeServer\Core\Text;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionKey;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\Core\Collections\Arr;

class PasswordRemindingLogic extends PageLogicBase
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
		//NOP
	}

	#endregion
}
