<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api;

use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\DomainLogicBase;
use PeServer\Core\Throws\NotImplementedException;

abstract class ApiLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeys(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function getUserInfo(): array|null
	{
		throw new NotImplementedException();
	}
}
