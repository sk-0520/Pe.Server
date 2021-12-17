<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;

class AccountLoginLogic extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$loginId = $this->getRequest('login-id');
		$this->validation->isNotWhiteSpace('login-id', $loginId);
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		//NONE
	}
}
