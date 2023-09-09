<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\AppEraser;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class AdministratorApiDeleteOldDataLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter, private AppEraser $appEraser)
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
		$this->appEraser->execute();

		$this->writeAuditLogCurrentUser(AuditLog::API_ADMINISTRATOR_DELETE_OLD_DATA);

		$this->setResponseJson(ResponseJson::success([]));
	}

	#endregion
}
