<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOptions;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Models\Domains\Page\Account\AccountLoginLogic;
use \PeServer\App\Models\Domains\Page\Account\AccountLogoutLogic;
use \PeServer\App\Models\Domains\Page\Account\AccountUserLogic;
use \PeServer\App\Models\Domains\Page\Account\AccountUserEditLogic;
use \PeServer\App\Models\SessionKey;
use \PeServer\App\Models\UserLevel;

final class AccountController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(ActionRequest $request, ActionOptions $options): void
	{
		if ($this->isLoggedIn()) {
			$this->user($request, $options);
		} else {
			$this->login_get($request, $options);
		}
	}

	public function login_get(ActionRequest $request, ActionOptions $options): void
	{
		if ($this->isLoggedIn()) {
			$this->redirectPath('/account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('login', $logic->getViewData());
	}

	public function login_post(ActionRequest $request, ActionOptions $options): void
	{
		if ($this->isLoggedIn()) {
			$this->redirectPath('/account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class, $request, $options);
		if ($logic->run(LogicCallMode::submit())) {
			if ($this->session->tryGet(SessionKey::ACCOUNT, $account)) {
				if ($account['level'] === UserLevel::SETUP) {
					$this->redirectPath('/setting/setup');
				}
			}

			$this->redirectPath('/');
		}

		$this->view('login', $logic->getViewData());
	}

	public function logout(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(AccountLogoutLogic::class, $request, $options);
		$logic->run(LogicCallMode::submit());

		$this->redirectPath('/');
	}

	public function user(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(AccountUserLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('user', $logic->getViewData());
	}

	public function user_edit_get(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(AccountUserEditLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('user_edit', $logic->getViewData());
	}

	public function user_edit_post(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(AccountUserEditLogic::class, $request, $options);
		if ($logic->run(LogicCallMode::submit())) {
			$this->redirectPath('/account/user');
		}

		$this->view('user_edit', $logic->getViewData());
	}
}
