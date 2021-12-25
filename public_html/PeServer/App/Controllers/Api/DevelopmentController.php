<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\ActionOptions;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Controllers\Api\ApiControllerBase;
use \PeServer\App\Models\Domains\Api\Development\DevelopmentInitializeLogic;


final class DevelopmentController extends ApiControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function initialize(ActionRequest $request, ActionOptions $options): void
	{
		// @phpstan-ignore-next-line DevelopmentInitializeLogic は phpstan 設定で読み込み除外(デプロイ処理周りの呼び出しなので全対応が現実的でない)
		$logic = $this->createLogic(DevelopmentInitializeLogic::class, $request, $options);
		$logic->run(LogicCallMode::submit());

		$this->data($logic->getResponse());
	}
}
