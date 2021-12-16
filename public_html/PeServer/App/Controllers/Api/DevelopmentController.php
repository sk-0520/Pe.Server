<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\ActionRequest;
use \PeServer\Core\Mvc\LogicMode;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Controllers\Api\ApiControllerBase;
use \PeServer\App\Models\Domains\Api\Development\DevelopmentInitializeLogic;

class DevelopmentController extends ApiControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function initialize(ActionRequest $request): void
	{
		// @phpstan-ignore-next-line
		$logic = $this->createLogic(DevelopmentInitializeLogic::class, $request);
		$logic->run(LogicMode::SUBMIT);

		$this->data($logic->getResponse());
	}
}
