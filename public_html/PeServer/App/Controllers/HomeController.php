<?php

declare(strict_types=1);

namespace PeServer\App\Controllers;

use \PeServer\Core\ActionRequest;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\App\Models\Domains\Home\HomeIndexLogic;

class HomeController extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function index(ActionRequest $request): void
	{
		$logic = $this->createLogic(HomeIndexLogic::class, $request);
		$logic->run(LogicCallMode::INITIALIZE);

		$this->view('index', $logic->getViewData());
	}
}
