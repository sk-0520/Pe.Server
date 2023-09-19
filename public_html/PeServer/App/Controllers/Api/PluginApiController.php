<?php

namespace PeServer\App\Controllers\Api;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Result\IActionResult;
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

	public function exists(): IActionResult
	{
		$logic = $this->createLogic(PluginApiExistsLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function generate_plugin_id(): IActionResult
	{
		$logic = $this->createLogic(PluginApiGeneratePluginIdLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function information(): IActionResult
	{
		$logic = $this->createLogic(PluginApiInformationLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}
}
