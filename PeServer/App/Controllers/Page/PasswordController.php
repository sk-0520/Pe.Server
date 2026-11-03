<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use Exception;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Password\PasswordReminderLogic;
use PeServer\App\Models\Domain\Page\Password\PasswordRemindingLogic;
use PeServer\App\Models\Domain\Page\Password\PasswordResetLogic;
use PeServer\App\Views\Pages\Password\PasswordReminderPage;
use PeServer\App\Views\Pages\Password\PasswordRemindingPage;
use PeServer\App\Views\Pages\Password\PasswordResetPage;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * [PAGE] パスワードコントローラ。
 */
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
		return $this->reminder_get2();
	}

	public function reminder_post(): IActionResult
	{
		return $this->reminder_post2();
	}

	public function reminding(): IActionResult
	{
		return $this->reminding2();
	}

	public function reset_get(): IActionResult
	{
		return $this->reset_get2();
	}

	public function reset_post(): IActionResult
	{
		return $this->reset_post2();
	}

	public function reminder_get2(): IActionResult
	{
		$logic = $this->createLogic(PasswordReminderLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(PasswordReminderPage::class, $logic->getViewData());
	}

	public function reminder_post2(): IActionResult
	{
		$logic = $this->createLogic(PasswordReminderLogic::class, ['$resetPath' => 'password/reset2']);
		if ($logic->run(LogicCallMode::Submit)) {
			if ($logic->tryGetResult('token', $token)) {
				return $this->redirectPath("password/reminding2/$token");
			}
			throw new InvalidOperationException();
		}

		return $this->view(PasswordReminderPage::class, $logic->getViewData());
	}

	public function reminding2(): IActionResult
	{
		$logic = $this->createLogic(PasswordRemindingLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(PasswordRemindingPage::class, $logic->getViewData());
	}

	public function reset_get2(): IActionResult
	{
		$logic = $this->createLogic(PasswordResetLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view(PasswordResetPage::class, $logic->getViewData());
	}

	public function reset_post2(): IActionResult
	{
		$logic = $this->createLogic(PasswordResetLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('/index2');
		}

		return $this->view(PasswordResetPage::class, $logic->getViewData());
	}
}
