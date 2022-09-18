<?php

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Management\ManagementBackupLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCacheRebuildLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementConfigurationLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCrashReportDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCrashReportListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDatabaseMaintenanceLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDefaultPluginLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementEnvironmentLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementFeedbackDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementFeedbackListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementLogDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementLogListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementMarkdownLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementPhpEvaluateLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementPluginCategoryListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementSetupLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateParameter;

final class ManagementController extends PageControllerBase
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
		$logic = $this->createLogic(ManagementSetupLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('setup', $logic->getViewData());
	}

	public function setup_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementSetupLogic::class);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('/');
		}

		return $this->view('setup', $logic->getViewData());
	}

	public function environment(): IActionResult
	{
		$logic = $this->createLogic(ManagementEnvironmentLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('environment', $logic->getViewData());
	}

	public function configuration(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('configuration', $logic->getViewData());
	}

	public function backup(): IActionResult
	{
		$logic = $this->createLogic(ManagementBackupLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->redirectPath('/management');
	}

	public function database_maintenance_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('database_maintenance', $logic->getViewData());
	}
	public function database_maintenance_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::submit());
		return $this->view('database_maintenance', $logic->getViewData());
	}

	public function php_evaluate_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('php_evaluate', $logic->getViewData());
	}
	public function php_evaluate_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::submit());
		return $this->view('php_evaluate', $logic->getViewData());
	}

	public function default_plugin_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementDefaultPluginLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function default_plugin_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementDefaultPluginLogic::class);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('management/default-plugin');
		}

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function cache_rebuild(): IActionResult
	{
		$logic = $this->createLogic(ManagementCacheRebuildLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->redirectPath('/management');
	}

	public function feedback_list_top(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackListLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('feedback_list', $logic->getViewData());
	}

	public function feedback_list_page(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackListLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->view('feedback_list', $logic->getViewData());
	}

	public function feedback_detail(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackDetailLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('feedback_detail', $logic->getViewData());
	}

	public function crash_report_list_top(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportListLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('crash_report_list', $logic->getViewData());
	}

	public function crash_report_list_page(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportListLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->view('crash_report_list', $logic->getViewData());
	}

	public function crash_report_detail(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportDetailLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('crash_report_detail', $logic->getViewData());
	}

	public function plugin_category_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementPluginCategoryListLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('plugin_category', $logic->getViewData());
	}

	public function log_list(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogListLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('log_list', $logic->getViewData());
	}

	public function log_detail(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogDetailLogic::class);
		$logic->run(LogicCallMode::initialize());

		if ($logic->equalsResult('download', true)) {
			return $this->data($logic->getContent());
		}

		return $this->view('log_detail', $logic->getViewData());
	}

	public function markdown(): IActionResult
	{
		$logic = $this->createLogic(ManagementMarkdownLogic::class);
		$logic->run(LogicCallMode::initialize());

		return $this->view('markdown', $logic->getViewData());
	}
}
