<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Api;

use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiCacheRebuildLogic;
use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiDeployLogic;
use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiPeVersionLogic;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;

/**
 * [API] 管理者用コントローラ。
 *
 * サイト管理的な処理を実施する重役。
 */
class AdministratorApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function deploy(): IActionResult
	{
		$logic = $this->createLogic(AdministratorApiDeployLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function pe_version(): IActionResult
	{
		$logic = $this->createLogic(AdministratorApiPeVersionLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}
}
