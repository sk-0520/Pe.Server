<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Plugin\PluginIndexLogic;
use PeServer\App\Models\Domain\Page\Plugin\PluginDetailLogic;
use PeServer\App\Views\Pages\Plugin\PluginIndexPage;
use PeServer\App\Views\Pages\Plugin\PluginDetailPage;

/**
 * [PAGE] プラグインコントローラ。
 */
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

		return $this->view(PluginIndexPage::class, $logic->getViewData());
	}

	public function detail(): IActionResult
	{
		$logic = $this->createLogic(PluginDetailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(PluginDetailPage::class, $logic->getViewData());
	}
}
