<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\NotImplementedException;

class AdministratorApiCrashReportLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		throw new NotImplementedException();
	}

	#endregion
}
