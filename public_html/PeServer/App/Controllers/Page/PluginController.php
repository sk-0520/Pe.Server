<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domains\Page\Plugin\PluginIndexLogic;



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
}
