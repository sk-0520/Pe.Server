<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\AccessLogManager;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\AppEraser;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class AdministratorApiVacuumAccessLogLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter, private AccessLogManager $accessLogManager)
	{
		parent::__construct($parameter);
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$this->accessLogManager->vacuum();

		$this->writeAuditLogCurrentUser(AuditLog::API_ADMINISTRATOR_VACUUM_ACCESS_LOG);

		$this->setResponseJson(ResponseJson::success([]));
	}

	#endregion
}
