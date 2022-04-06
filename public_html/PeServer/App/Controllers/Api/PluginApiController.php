<?php

namespace PeServer\App\Controllers\Api;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Api\ApiControllerBase;
use PeServer\App\Models\Domain\Api\PluginApi\PluginApiExistsLogic;
use PeServer\App\Models\Domain\Api\PluginApi\PluginApiInformationLogic;
use PeServer\App\Models\Domain\Api\PluginApi\PluginApiGeneratePluginIdLogic;


class PluginApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function exists(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(PluginApiExistsLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function generate_plugin_id(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(PluginApiGeneratePluginIdLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}

	public function information(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(PluginApiInformationLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getContent());
	}
}
