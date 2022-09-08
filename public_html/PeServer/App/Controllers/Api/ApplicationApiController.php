<?php

namespace PeServer\App\Controllers\Api;

use PeServer\App\Controllers\Api\ApiControllerBase;
use PeServer\App\Models\Domain\Api\ApplicationApi\ApplicationApiFeedbackLogic;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;

class ApplicationApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function feedback(): IActionResult
	{
		$logic = $this->createLogic(ApplicationApiFeedbackLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}
}
