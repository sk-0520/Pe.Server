<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOption;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Controllers\DomainControllerBase;
use \PeServer\App\Models\Domains\Page\Setting\SettingSetupLogic;

final class SettingController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function setup_get(ActionRequest $request): void
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		$this->view('setup', $logic->getViewData());
	}
	public function setup_post(ActionRequest $request): void
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			$this->redirectPath('/');
		}

		$this->view('setup', $logic->getViewData());
	}
}
