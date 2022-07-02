<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Ajax\AjaxMarkdownLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxPluginCategoryCreateLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxPluginCategoryDeleteLogic;
use PeServer\App\Models\Domain\Page\Ajax\AjaxPluginCategoryUpdateLogic;

final class AjaxController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function markdown(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AjaxMarkdownLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function plugin_category_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryCreateLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function plugin_category_patch(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryUpdateLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function plugin_category_delete(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryDeleteLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}
}
