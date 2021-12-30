<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOption;
use \PeServer\Core\Mvc\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Models\Domains\Page\Account\AccountLoginLogic;
use \PeServer\App\Models\Domains\Page\Account\AccountLogoutLogic;
use \PeServer\App\Models\Domains\Page\Account\AccountUserLogic;
use \PeServer\App\Models\Domains\Page\Account\AccountUserEditLogic;
use \PeServer\App\Models\SessionKey;
use \PeServer\App\Models\UserLevel;
use \PeServer\Core\Mvc\IActionResult;

final class AccountController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(ActionRequest $request): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->user($request);
		} else {
			return $this->login_get($request);
		}
	}

	public function login_get(ActionRequest $request): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->redirectPath('/account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('login', $logic->getViewData());
	}

	public function login_post(ActionRequest $request): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->redirectPath('/account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			if ($this->session->tryGet(SessionKey::ACCOUNT, $account)) {
				if ($account['level'] === UserLevel::SETUP) {
					return $this->redirectPath('/setting/setup');
				}
			}

			return $this->redirectPath('/');
		}

		return $this->view('login', $logic->getViewData());
	}

	public function logout(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountLogoutLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->redirectPath('/');
	}

	public function user(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user', $logic->getViewData());
	}

	public function user_edit_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEditLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_edit', $logic->getViewData());
	}

	public function user_edit_post(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEditLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('/account/user');
		}

		return $this->view('user_edit', $logic->getViewData());
	}
}
