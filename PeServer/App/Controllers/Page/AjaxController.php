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
use PeServer\Core\Binary;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\CallbackChunkedContent;
use PeServer\Core\Mvc\Content\CallbackEventStreamContent;
use PeServer\Core\Mvc\Content\EventStreamMessage;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\Logic\LogicCallMode;
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


	private function dev_streaming_wait(): void
	{
		//usleep(500);
		sleep(1);
	}

	public function dev_streaming_chunk(): IActionResult
	{
		return $this->data(new CallbackChunkedContent(function () {
			$this->dev_streaming_wait();
			yield new Binary("abc");
			$this->dev_streaming_wait();
			yield new Binary("defghi");
			$this->dev_streaming_wait();
			yield new Binary("jklmnoopq");
			$this->dev_streaming_wait();
			yield new Binary("rstuvwxyz012");
		}, Mime::TEXT));
	}

	public function dev_streaming_sse_text(): IActionResult
	{
		return $this->data(new CallbackEventStreamContent(function () {
			$this->dev_streaming_wait();
			yield new EventStreamMessage("abc");
			$this->dev_streaming_wait();
			yield new EventStreamMessage("defghi");
			$this->dev_streaming_wait();
			yield new EventStreamMessage("jklmnoopq");
			$this->dev_streaming_wait();
			yield new EventStreamMessage("rstuvwxyz012");
		}));
	}

	public function dev_streaming_sse_json(): IActionResult
	{
		return $this->data(new CallbackEventStreamContent(function () {
			$this->dev_streaming_wait();
			yield new EventStreamMessage(["content" => "abc"]);
			$this->dev_streaming_wait();
			yield new EventStreamMessage(["content" => "defghi"]);
			$this->dev_streaming_wait();
			yield new EventStreamMessage(["content" => "jklmnoopq"]);
			$this->dev_streaming_wait();
			yield new EventStreamMessage(["content" => "rstuvwxyz012"]);
		}));
	}
}
