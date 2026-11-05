<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Management\ManagementBackupLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementCacheRebuildLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementClearDeployProgressLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementConfigurationEditLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementConfigurationLogic;
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
use PeServer\App\Models\Domain\Page\Management\ManagementVacuumAccessLogLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementVersionLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\App\Views\Pages\Management\ManagementIndexPage;
use PeServer\App\Views\Pages\Management\ManagementSetupPage;
use PeServer\App\Views\Pages\Management\ManagementEnvironmentPage;
use PeServer\App\Views\Pages\Management\ManagementConfigurationPage;
use PeServer\App\Views\Pages\Management\ManagementConfigurationEditPage;
use PeServer\App\Views\Pages\Management\ManagementDatabaseMaintenancePage;
use PeServer\App\Views\Pages\Management\ManagementMailSendPage;
use PeServer\App\Views\Pages\Management\ManagementPhpEvaluatePage;
use PeServer\App\Views\Pages\Management\ManagementDefaultPluginPage;
use PeServer\App\Views\Pages\Management\ManagementFeedbackListPage;
use PeServer\App\Views\Pages\Management\ManagementFeedbackDetailPage;
use PeServer\App\Views\Pages\Management\ManagementCrashReportListPage;
use PeServer\App\Views\Pages\Management\ManagementCrashReportDetailPage;
use PeServer\App\Views\Pages\Management\ManagementVersionPage;
use PeServer\App\Views\Pages\Management\ManagementPluginCategoryPage;
use PeServer\App\Views\Pages\Management\ManagementLogListPage;
use PeServer\App\Views\Pages\Management\ManagementLogDetailPage;
use PeServer\App\Views\Pages\Management\ManagementMarkdownPage;

/**
 * [PAGE] 管理者用コントローラ。
 */
final class ManagementController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(): IActionResult
	{
		$databases = [];

		$databases["session"] = [
			"target" => "session",
			"display" => "セッション",
		];

		return $this->view(ManagementIndexPage::class, new TemplateParameter(HttpStatus::OK, [
			"databases" => $databases
		], []));
	}

	public function setup_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementSetupLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementSetupPage::class, $logic->getViewData());
	}

	public function setup_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementSetupLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('/');
		}

		return $this->view(ManagementSetupPage::class, $logic->getViewData());
	}

	public function environment(): IActionResult
	{
		$logic = $this->createLogic(ManagementEnvironmentLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementEnvironmentPage::class, $logic->getViewData());
	}

	public function configuration(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementConfigurationPage::class, $logic->getViewData());
	}

	public function configuration_edit_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationEditLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementConfigurationEditPage::class, $logic->getViewData());
	}

	public function configuration_edit_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementConfigurationEditLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/configuration/edit');
		}

		return $this->view(ManagementConfigurationEditPage::class, $logic->getViewData());
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

		return $this->view(ManagementDatabaseMaintenancePage::class, $logic->getViewData());
	}
	public function database_maintenance_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementDatabaseMaintenanceLogic::class);
		$logic->run(LogicCallMode::Submit);
		return $this->view(ManagementDatabaseMaintenancePage::class, $logic->getViewData());
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
		return $this->view(ManagementMailSendPage::class, $logic->getViewData());
	}
	public function mail_send_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementMailSendLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/mail-send');
		}

		return $this->view(ManagementMailSendPage::class, $logic->getViewData());
	}

	public function php_evaluate_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementPhpEvaluatePage::class, $logic->getViewData());
	}
	public function php_evaluate_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementPhpEvaluateLogic::class);
		$logic->run(LogicCallMode::Submit);
		return $this->view(ManagementPhpEvaluatePage::class, $logic->getViewData());
	}

	public function default_plugin_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementDefaultPluginLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementDefaultPluginPage::class, $logic->getViewData());
	}

	public function default_plugin_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementDefaultPluginLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/default-plugin');
		}

		return $this->view(ManagementDefaultPluginPage::class, $logic->getViewData());
	}

	public function cache_rebuild(): IActionResult
	{
		$logic = $this->createLogic(ManagementCacheRebuildLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function vacuum_access_log(): IActionResult
	{
		$logic = $this->createLogic(ManagementVacuumAccessLogLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function clear_deploy_progress(): IActionResult
	{
		$logic = $this->createLogic(ManagementClearDeployProgressLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/management');
	}

	public function feedback_list_top(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementFeedbackListPage::class, $logic->getViewData());
	}

	public function feedback_list_page(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackListLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->view(ManagementFeedbackListPage::class, $logic->getViewData());
	}

	public function feedback_detail_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementFeedbackDetailPage::class, $logic->getViewData());
	}

	public function feedback_detail_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementFeedbackDetailLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			if ($logic->tryGetResult('sequence', $sequence)) {
				return $this->redirectPath("management/feedback/$sequence");
			}
			throw new InvalidOperationException();
		}

		return $this->view(ManagementFeedbackDetailPage::class, $logic->getViewData());
	}

	public function crash_report_list_top(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementCrashReportListPage::class, $logic->getViewData());
	}

	public function crash_report_list_page(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportListLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->view(ManagementCrashReportListPage::class, $logic->getViewData());
	}

	public function crash_report_detail_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementCrashReportDetailPage::class, $logic->getViewData());
	}

	public function crash_report_detail_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementCrashReportDetailLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			if ($logic->tryGetResult('sequence', $sequence)) {
				return $this->redirectPath("management/crash-report/$sequence");
			}
			throw new InvalidOperationException();
		}

		return $this->view(ManagementCrashReportDetailPage::class, $logic->getViewData());
	}

	public function version_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementVersionLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementVersionPage::class, $logic->getViewData());
	}

	public function version_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementVersionLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('management/version');
		}

		return $this->view(ManagementVersionPage::class, $logic->getViewData());
	}

	public function plugin_category_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementPluginCategoryListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementPluginCategoryPage::class, $logic->getViewData());
	}

	public function log_list(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementLogListPage::class, $logic->getViewData());
	}

	public function log_detail_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		if ($logic->equalsResult('download', true)) {
			return $this->data($logic->getContent());
		}

		return $this->view(ManagementLogDetailPage::class, $logic->getViewData());
	}

	public function log_detail_post(): IActionResult
	{
		$logic = $this->createLogic(ManagementLogDetailLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function markdown(): IActionResult
	{
		$logic = $this->createLogic(ManagementMarkdownLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(ManagementMarkdownPage::class, $logic->getViewData());
	}
}
