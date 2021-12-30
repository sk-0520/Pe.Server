<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOption;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\Mvc\IActionResult;
use \PeServer\Core\Mvc\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Models\Domains\Page\Home\HomeIndexLogic;
use \PeServer\App\Models\Domains\Page\Home\HomeContactLogic;
use \PeServer\App\Models\Domains\Page\Home\HomePrivacyLogic;


final class HomeController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeIndexLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('index', $logic->getViewData());
	}

	public function privacy(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomePrivacyLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('privacy', $logic->getViewData());
	}

	public function contact_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeContactLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('contact', $logic->getViewData());
	}
}
