<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api;

use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domain\DomainLogicBase;
use PeServer\Core\Throws\NotImplementedException;

abstract class ApiLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getAuditUserInfo(): array|null
	{
		throw new NotImplementedException();
	}
}
