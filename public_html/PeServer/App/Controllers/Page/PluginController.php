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

	public function index(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(PluginIndexLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('index', $logic->getViewData());
	}

	public function detail(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(PluginDetailLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('detail', $logic->getViewData());
	}
}
