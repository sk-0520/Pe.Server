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

	public function markdown(): IActionResult
	{
		$logic = $this->createLogic(AjaxMarkdownLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function plugin_category_post(): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryCreateLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function plugin_category_patch(): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryUpdateLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function plugin_category_delete(): IActionResult
	{
		$logic = $this->createLogic(AjaxPluginCategoryDeleteLogic::class);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}
}
