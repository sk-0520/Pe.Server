<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domains\Page\Ajax\AjaxMarkdownLogic;

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
}
