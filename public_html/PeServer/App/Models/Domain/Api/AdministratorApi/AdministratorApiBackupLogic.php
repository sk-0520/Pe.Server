<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class AdministratorApiBackupLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter, private AppArchiver $appArchiver)
	{
		parent::__construct($parameter);
	}

	//[ApiLogicBase]

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$size = $this->appArchiver->backup();
		$this->appArchiver->rotate();

		$this->setResponseJson(ResponseJson::success([
			'success' => true,
			'size' => $size,
		]));
	}
}
