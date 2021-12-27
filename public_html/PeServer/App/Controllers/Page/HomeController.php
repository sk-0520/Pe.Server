<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOptions;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\App\Models\Domains\Page\Home\HomeIndexLogic;
use \PeServer\App\Models\Domains\Page\Home\HomePrivacyLogic;
use \PeServer\App\Models\Domains\Page\Home\HomeContactLogic;


final class HomeController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(HomeIndexLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('index', $logic->getViewData());
	}

	public function privacy(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(HomePrivacyLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('privacy', $logic->getViewData());
	}

	public function contact_get(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(HomeContactLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('contact', $logic->getViewData());
	}
}
