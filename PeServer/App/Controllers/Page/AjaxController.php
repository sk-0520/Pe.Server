<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Ajax\AjaxCrashReportDeleteLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxFeedbackDeleteLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxLogFileDeleteLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxMarkdownLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxPluginCategoryCreateLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxPluginCategoryDeleteLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxPluginCategoryUpdateLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;

/**
 * [PAGE] ページ内の Ajax 処理統括コントローラ。
 */
final class AjaxController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function markdown(): IActionResult
	{
		$logic = $this->createLogic(AjaxMarkdownLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function plugin_category_post(): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryCreateLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function plugin_category_patch(): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryUpdateLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function plugin_category_delete(): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryDeleteLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function log_delete(): IActionResult
	{
		$logic = $this->createLogic(AjaxLogFileDeleteLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function feedback_delete(): IActionResult
	{
		$logic = $this->createLogic(AjaxFeedbackDeleteLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function crash_report_delete(): IActionResult
	{
		$logic = $this->createLogic(AjaxCrashReportDeleteLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}
}
