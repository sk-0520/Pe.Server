<?php

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Management\ManagementBackupLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCacheRebuildLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementClearDeployProgressLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementConfigurationLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementConfigurationEditLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCrashReportDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCrashReportListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDatabaseDownloadLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDatabaseMaintenanceLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDefaultPluginLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDeleteOldDataLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementEnvironmentLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementFeedbackDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementFeedbackListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementLogDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementLogListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementMailSendLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementMarkdownLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementPhpEvaluateLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementPluginCategoryListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementSetupLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementVersionLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Throws\InvalidOperationException;

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
		$logic->run(LogicCallMode::Initialize);

		return $this->view('setup', $logic->getViewData());
	}

	public function setup_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementSetupLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('/');
		}

		return $this->view('setup', $logic->getViewData());
	}

	public function environment(): IActionResult
	{
		$logic = $this->createLogic(ManagementEnvironmentLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('environment', $logic->getViewData());
	}

	public function configuration(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('configuration', $logic->getViewData());
	}

	public function configuration_edit_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationEditLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('configuration_edit', $logic->getViewData());
	}

	public function configuration_edit_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationEditLogic::class);
		if($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/configuration/edit');
		}

		return $this->view('configuration_edit', $logic->getViewData());
	}

	public function backup(): IActionResult
	{
		$logic = $this->createLogic(ManagementBackupLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function delete_old_data(): IActionResult
	{
		$logic = $this->createLogic(ManagementDeleteOldDataLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function database_maintenance_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('database_maintenance', $logic->getViewData());
	}
	public function database_maintenance_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::Submit);
		return $this->view('database_maintenance', $logic->getViewData());
	}
	public function database_download_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementDatabaseDownloadLogic::class);
		$logic->run(LogicCallMode::Submit);
		return $this->data($logic->getContent());
	}

	public function mail_send_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementMailSendLogic::class);
		$logic->run(LogicCallMode::Initialize);
		return $this->view('mail_send', $logic->getViewData());
	}
	public function mail_send_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementMailSendLogic::class);
		if($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/mail-send');
		}

		return $this->view('mail_send', $logic->getViewData());
	}

	public function php_evaluate_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('php_evaluate', $logic->getViewData());
	}
	public function php_evaluate_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::Submit);
		return $this->view('php_evaluate', $logic->getViewData());
	}

	public function default_plugin_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementDefaultPluginLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function default_plugin_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementDefaultPluginLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/default-plugin');
		}

		return $this->view('default_plugin', $logic->getViewData());
	}

	public function cache_rebuild(): IActionResult
	{
		$logic = $this->createLogic(ManagementCacheRebuildLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function clear_deploy_progress(): IActionResult {
		$logic = $this->createLogic(ManagementClearDeployProgressLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function feedback_list_top(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('feedback_list', $logic->getViewData());
	}

	public function feedback_list_page(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackListLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->view('feedback_list', $logic->getViewData());
	}

	public function feedback_detail_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('feedback_detail', $logic->getViewData());
	}

	public function feedback_detail_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackDetailLogic::class);
		if($logic->run(LogicCallMode::Submit)) {
			if ($logic->tryGetResult('sequence', $sequence)) {
				return $this->redirectPath("management/feedback/$sequence");
			}
			throw new InvalidOperationException();
		}

		return $this->view('feedback_detail', $logic->getViewData());
	}

	public function crash_report_list_top(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('crash_report_list', $logic->getViewData());
	}

	public function crash_report_list_page(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportListLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->view('crash_report_list', $logic->getViewData());
	}

	public function crash_report_detail_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('crash_report_detail', $logic->getViewData());
	}

	public function crash_report_detail_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportDetailLogic::class);
		if($logic->run(LogicCallMode::Submit)) {
			if ($logic->tryGetResult('sequence', $sequence)) {
				return $this->redirectPath("management/crash-report/$sequence");
			}
			throw new InvalidOperationException();
		}

		return $this->view('crash_report_detail', $logic->getViewData());
	}

	public function version_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementVersionLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('version', $logic->getViewData());
	}

	public function version_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementVersionLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/version');
		}

		return $this->view('version', $logic->getViewData());
	}

	public function plugin_category_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementPluginCategoryListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('plugin_category', $logic->getViewData());
	}

	public function log_list(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('log_list', $logic->getViewData());
	}

	public function log_detail(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		if ($logic->equalsResult('download', true)) {
			return $this->data($logic->getContent());
		}

		return $this->view('log_detail', $logic->getViewData());
	}

	public function markdown(): IActionResult
	{
		$logic = $this->createLogic(ManagementMarkdownLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('markdown', $logic->getViewData());
	}
}
