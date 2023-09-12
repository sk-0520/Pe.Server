<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Password\PasswordReminderLogic;
use PeServer\App\Models\Domain\Page\Password\PasswordRemindingLogic;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;


final class PasswordController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	// public function index(): IActionResult
	// {
	// }

	public function reminder_get(): IActionResult
	{
		$logic = $this->createLogic(PasswordReminderLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('reminder', $logic->getViewData());
	}

	public function reminder_post(): IActionResult
	{
		$logic = $this->createLogic(PasswordReminderLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('password/reminding');
		}

		return $this->view('reminder', $logic->getViewData());
	}

	public function reminding(): IActionResult
	{
		$logic = $this->createLogic(PasswordRemindingLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('reminding', $logic->getViewData());
	}
}
