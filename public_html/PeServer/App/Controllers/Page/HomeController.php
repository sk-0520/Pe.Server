<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Http\HttpStatusCode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Models\Domain\Page\Home\HomeAboutLogic;
use PeServer\App\Models\Domain\Page\Home\HomeIndexLogic;
use PeServer\App\Models\Domain\Page\Home\HomeContactLogic;
use PeServer\App\Models\Domain\Page\Home\HomePrivacyLogic;


final class HomeController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeIndexLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('index', $logic->getViewData());
	}

	public function privacy(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomePrivacyLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('privacy', $logic->getViewData());
	}

	public function contact_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeContactLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('contact', $logic->getViewData());
	}
	public function about(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeAboutLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('about', $logic->getViewData());
	}
}
