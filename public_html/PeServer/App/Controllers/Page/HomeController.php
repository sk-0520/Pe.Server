<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Home\HomeAboutLogic;
use PeServer\App\Models\Domain\Page\Home\HomeApiDocumentLogic;
use PeServer\App\Models\Domain\Page\Home\HomeContactLogic;
use PeServer\App\Models\Domain\Page\Home\HomeIndexLogic;
use PeServer\App\Models\Domain\Page\Home\HomePrivacyLogic;
use PeServer\App\Models\Domain\Page\Home\HomeWildcardLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;


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

	public function api(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeApiDocumentLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('api', $logic->getViewData());
	}

	public function wildcard(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(HomeWildcardLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->data($logic->getContent());
	}

}
