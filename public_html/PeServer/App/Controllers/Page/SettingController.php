<?php

namespace PeServer\App\Controllers\Page;

use PeServer\Core\HttpStatus;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\DomainControllerBase;
use PeServer\App\Models\Domains\Page\Setting\SettingSetupLogic;
use PeServer\App\Models\Domains\Page\Setting\SettingDefaultPluginLogic;

final class SettingController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(ActionRequest $request): IActionResult
	{
		return $this->view('index', new TemplateParameter(HttpStatus::ok(), [], []));
	}

	public function setup_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('setup', $logic->getViewData());
	}

	public function setup_post(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('/');
		}

		return $this->view('setup', $logic->getViewData());
	}

	public function environment(ActionRequest $request): IActionResult
	{
		return $this->view('environment', new TemplateParameter(HttpStatus::ok(), [], []));
	}

	public function default_plugin_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingDefaultPluginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function default_plugin_post(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingDefaultPluginLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('setting/default-plugin');
		}

		return $this->view('default_plugin', $logic->getViewData());
	}
}
