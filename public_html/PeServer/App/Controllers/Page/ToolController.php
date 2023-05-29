<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Tool\ToolBase64Logic;
use PeServer\App\Models\Domain\Page\Tool\ToolJsonLogic;
use PeServer\App\Models\Domain\Page\Tool\ToolIndexLogic;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;

final class ToolController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	public function index(): IActionResult
	{
		$logic = $this->createLogic(ToolIndexLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('index', $logic->getViewData());
	}

	public function base64_get(): IActionResult
	{
		$logic = $this->createLogic(ToolBase64Logic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('base64', $logic->getViewData());
	}

	public function base64_post(): IActionResult
	{
		$logic = $this->createLogic(ToolBase64Logic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->view('base64', $logic->getViewData());
	}

	public function json_get(): IActionResult
	{
		$logic = $this->createLogic(ToolJsonLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('json', $logic->getViewData());
	}

	public function json_post(): IActionResult
	{
		$logic = $this->createLogic(ToolJsonLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->view('json', $logic->getViewData());
	}

	#endregion
}
