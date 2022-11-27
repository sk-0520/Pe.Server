<?php

namespace PeServer\App\Controllers\Api;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Api\ApiControllerBase;
use PeServer\App\Models\Domain\Api\DevelopmentApi\DevelopmentApiInitializeLogic;
use PeServer\App\Models\Domain\Api\DevelopmentApi\DevelopmentApiAdministratorLogic;

final class DevelopmentApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function initialize(): IActionResult
	{
		$logic = $this->createLogic(DevelopmentApiInitializeLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function administrator(): IActionResult
	{
		$logic = $this->createLogic(DevelopmentApiAdministratorLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}
}
