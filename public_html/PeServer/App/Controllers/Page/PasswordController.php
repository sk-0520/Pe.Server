<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use Exception;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Password\PasswordReminderLogic;
use PeServer\App\Models\Domain\Page\Password\PasswordRemindingLogic;
use PeServer\App\Models\Domain\Page\Password\PasswordResetLogic;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Throws\InvalidOperationException;


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
			if ($logic->tryGetResult('token', $token)) {
				return $this->redirectPath("password/reminding/$token");
			}
			throw new InvalidOperationException();
		}

		return $this->view('reminder', $logic->getViewData());
	}

	public function reminding(): IActionResult
	{
		$logic = $this->createLogic(PasswordRemindingLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('reminding', $logic->getViewData());
	}

	public function reset_get(): IActionResult
	{
		$logic = $this->createLogic(PasswordResetLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('reset', $logic->getViewData());
	}

	public function reset_post(): IActionResult
	{
		$logic = $this->createLogic(PasswordResetLogic::class);
		if ($logic->run(LogicCallMode::Initialize)) {
			// TODO: パスワード系に移すかトップに飛ばすか、ログインさせるか
			throw new Exception('TODO');
		}

		return $this->view('reset', $logic->getViewData());
	}
}
