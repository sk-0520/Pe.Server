<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class AdministratorApiPeVersionGetLogic extends ApiLogicBase
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
		$database = $this->openDatabase();

		$peSettingEntityDao = new PeSettingEntityDao($database);

		$version = $peSettingEntityDao->selectVersion();

		$result = [
			'version' => $version
		];

		$this->setResponseJson(ResponseJson::success($result));
	}

	#endregion
}
