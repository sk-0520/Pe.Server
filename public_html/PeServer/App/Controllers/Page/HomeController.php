<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionRequest;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\App\Models\Domains\Page\Home\HomeIndexLogic;
use \PeServer\App\Models\Domains\Page\Home\HomePrivacyLogic;
use \PeServer\App\Models\Domains\Page\Home\HomeContactLogic;


class HomeController extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function index(ActionRequest $request): void
	{
		$logic = $this->createLogic(HomeIndexLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		$this->view('index', $logic->getViewData());
	}

	public function privacy(ActionRequest $request): void
	{
		$logic = $this->createLogic(HomePrivacyLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		$this->view('privacy', $logic->getViewData());
	}

	public function contact_get(ActionRequest $request): void
	{
		$logic = $this->createLogic(HomeContactLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		$this->view('contact', $logic->getViewData());
	}
}
