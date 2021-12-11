<?php

declare(strict_types=1);

namespace PeServer\App\Controllers;

use \PeServer\Core\ActionRequest;
use \PeServer\Core\ControllerArguments;
use \PeServer\Core\ControllerBase;
use \PeServer\Core\LogicMode;
use \PeServer\App\Models\Domains\Home\HomeIndexLogic;
use PeServer\Core\HttpStatusCode;

class HomeController extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function index(ActionRequest $request)
	{
		$logic = $this->createLogic(HomeIndexLogic::class, $request);
		$logic->run(LogicMode::INITIALIZE);

		return $this->view('index', $logic->getViewData());
	}
}
