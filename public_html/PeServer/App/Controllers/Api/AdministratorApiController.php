<?php

namespace PeServer\App\Controllers\Api;

use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiBackupLogic;
use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiCacheRebuildLogic;
use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiDeployLogic;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;

class AdministratorApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function backup(): IActionResult
	{
		$logic = $this->createLogic(AdministratorApiBackupLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function cache_rebuild(): IActionResult
	{
		$logic = $this->createLogic(AdministratorApiCacheRebuildLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function deploy(): IActionResult
	{
		$logic = $this->createLogic(AdministratorApiDeployLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}
}
