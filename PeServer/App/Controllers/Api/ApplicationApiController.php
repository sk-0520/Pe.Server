<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Api;

use PeServer\App\Controllers\Api\ApiControllerBase;
use PeServer\App\Models\Domain\Api\ApplicationApi\ApplicationApiCrashReportLogic;
use PeServer\App\Models\Domain\Api\ApplicationApi\ApplicationApiFeedbackLogic;
use PeServer\App\Models\Domain\Api\ApplicationApi\ApplicationApiVersionUpdateLogic;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Result\RedirectActionResult;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * [API] Peから呼び出されるコントローラ。
 *
 * 管理やら開発都合でなくユーザー用に外向いているのはこいつ(1)。
 */
class ApplicationApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	public function feedback(): IActionResult
	{
		$logic = $this->createLogic(ApplicationApiFeedbackLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function crash_report(): IActionResult
	{
		$logic = $this->createLogic(ApplicationApiCrashReportLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}

	public function version_update(): IActionResult
	{
		$logic = $this->createLogic(ApplicationApiVersionUpdateLogic::class);
		$logic->run(LogicCallMode::Submit);

		if ($logic->redirectUrl === null) {
			throw new InvalidOperationException();
		}

		return new RedirectActionResult($logic->redirectUrl, HttpStatus::Found);
	}

	#endregion
}
