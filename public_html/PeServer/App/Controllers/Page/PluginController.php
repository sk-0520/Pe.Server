<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Plugin\PluginIndexLogic;
use PeServer\App\Models\Domain\Page\Plugin\PluginDetailLogic;



final class PluginController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(): IActionResult
	{
		$logic = $this->createLogic(PluginIndexLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('index', $logic->getViewData());
	}

	public function detail(): IActionResult
	{
		$logic = $this->createLogic(PluginDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('detail', $logic->getViewData());
	}
}
