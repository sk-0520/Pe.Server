<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOptions;
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

	public function setup_get(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('setup', $logic->getViewData());
	}
	public function setup_post(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request, $options);
		if ($logic->run(LogicCallMode::submit())) {
			$this->redirectPath('/');
		}

		$this->view('setup', $logic->getViewData());
	}
}
