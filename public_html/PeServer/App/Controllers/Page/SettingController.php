<?php

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Setting\SettingSetupLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingLogListLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingMarkdownLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingLogDetailLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingDefaultPluginLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingEnvironmentPluginLogic;

final class SettingController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(HttpRequest $request): IActionResult
	{
		return $this->view('index', new TemplateParameter(HttpStatus::ok(), [], []));
	}

	public function setup_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('setup', $logic->getViewData());
	}

	public function setup_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingSetupLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('/');
		}

		return $this->view('setup', $logic->getViewData());
	}

	public function environment(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingEnvironmentPluginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('environment', $logic->getViewData());
	}

	public function default_plugin_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingDefaultPluginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function default_plugin_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingDefaultPluginLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('setting/default-plugin');
		}

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function log_list(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingLogListLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('log_list', $logic->getViewData());
	}

	public function log_detail(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingLogDetailLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		if ($logic->equalsResult('download', true)) {
			return $this->data($logic->getContent());
		}

		return $this->view('log_detail', $logic->getViewData());
	}

	public function markdown(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(SettingMarkdownLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('markdown', $logic->getViewData());
	}

}
