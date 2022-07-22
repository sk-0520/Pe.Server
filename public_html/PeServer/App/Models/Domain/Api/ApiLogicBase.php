<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api;

use PeServer\App\Models\Domain\DomainLogicBase;
use PeServer\App\Models\IAuditUserInfo;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\NotImplementedException;

abstract class ApiLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getAuditUserInfo(): ?IAuditUserInfo
	{
		throw new NotImplementedException();
	}
}
