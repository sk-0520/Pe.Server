<?php

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Setting\SettingCacheRebuildLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingConfigurationLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingDatabaseMaintenanceLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingDefaultPluginLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingEnvironmentLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingLogDetailLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingLogListLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingMarkdownLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingPhpEvaluateLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingPluginCategoryListLogic;
use PeServer\App\Models\Domain\Page\Setting\SettingSetupLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateParameter;

final class SettingController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(): IActionResult
	{
		return $this->view('index', new TemplateParameter(HttpStatus::ok(), [], []));
	}

	public function setup_get(): IActionResult
	{
		$logic = $this->createLogic(SettingSetupLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('setup', $logic->getViewData());
	}

	public function setup_post(): IActionResult
	{
		$logic = $this->createLogic(SettingSetupLogic::class);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('/');
		}

		return $this->view('setup', $logic->getViewData());
	}

	public function environment(): IActionResult
	{
		$logic = $this->createLogic(SettingEnvironmentLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('environment', $logic->getViewData());
	}

	public function configuration(): IActionResult
	{
		$logic = $this->createLogic(SettingConfigurationLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('configuration', $logic->getViewData());
	}

	public function database_maintenance_get(): IActionResult
	{
		$logic = $this->createLogic(SettingDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('database_maintenance', $logic->getViewData());
	}
	public function database_maintenance_post(): IActionResult
	{
		$logic = $this->createLogic(SettingDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::submit());
		return $this->view('database_maintenance', $logic->getViewData());
	}

	public function php_evaluate_get(): IActionResult
	{
		$logic = $this->createLogic(SettingPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('php_evaluate', $logic->getViewData());
	}
	public function php_evaluate_post(): IActionResult
	{
		$logic = $this->createLogic(SettingPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::submit());
		return $this->view('php_evaluate', $logic->getViewData());
	}

	public function default_plugin_get(): IActionResult
	{
		$logic = $this->createLogic(SettingDefaultPluginLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function default_plugin_post(): IActionResult
	{
		$logic = $this->createLogic(SettingDefaultPluginLogic::class);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('setting/default-plugin');
		}

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function cache_rebuild(): IActionResult
	{
		$logic = $this->createLogic(SettingCacheRebuildLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->redirectPath('/setting');

	}

	public function plugin_category_get(): IActionResult
	{
		$logic = $this->createLogic(SettingPluginCategoryListLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('plugin_category', $logic->getViewData());
	}

	public function log_list(): IActionResult
	{
		$logic = $this->createLogic(SettingLogListLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('log_list', $logic->getViewData());
	}

	public function log_detail(): IActionResult
	{
		$logic = $this->createLogic(SettingLogDetailLogic::class);
		$logic->run(LogicCallMode::initialize());

		if ($logic->equalsResult('download', true)) {
			return $this->data($logic->getContent());
		}

		return $this->view('log_detail', $logic->getViewData());
	}

	public function markdown(): IActionResult
	{
		$logic = $this->createLogic(SettingMarkdownLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('markdown', $logic->getViewData());
	}
}
