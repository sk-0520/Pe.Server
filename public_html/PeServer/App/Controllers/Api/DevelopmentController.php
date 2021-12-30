<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\ActionOption;
use \PeServer\Core\Mvc\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Controllers\Api\ApiControllerBase;
use \PeServer\App\Models\Domains\Api\Development\DevelopmentInitializeLogic;
use \PeServer\App\Models\Domains\Api\Development\DevelopmentAdministratorLogic;
use \PeServer\Core\Mvc\IActionResult;

final class DevelopmentController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function initialize(ActionRequest $request): IActionResult
	{
		// @phpstan-ignore-next-line DevelopmentInitializeLogic は phpstan 設定で読み込み除外(デプロイ処理周りの呼び出しなので全対応が現実的でない)
		$logic = $this->createLogic(DevelopmentInitializeLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getResponse());
	}

	public function administrator(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(DevelopmentAdministratorLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->data($logic->getResponse());
	}
}
