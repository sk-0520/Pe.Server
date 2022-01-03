<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Models\Domains\UserLevel;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Models\Domains\Page\Account\AccountUserLogic;
use PeServer\App\Models\Domains\Page\Account\AccountLoginLogic;
use PeServer\App\Models\Domains\Page\Account\AccountLogoutLogic;
use PeServer\App\Models\Domains\Page\Account\AccountUserEditLogic;
use PeServer\App\Models\Domains\Page\Account\AccountUserEmailLogic;
use PeServer\App\Models\Domains\Page\Account\AccountUserPluginLogic;
use PeServer\App\Models\Domains\Page\Account\AccountUserPasswordLogic;
use PeServer\Core\Throws\InvalidOperationException;

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
			if ($this->session->tryGet(SessionManager::ACCOUNT, $account)) {
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

	public function user_password_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserPasswordLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_password', $logic->getViewData());
	}

	public function user_password_post(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserPasswordLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('/account/user');
		}

		return $this->view('user_password', $logic->getViewData());
	}

	public function user_email_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEmailLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_email', $logic->getViewData());
	}

	public function user_email_post(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEmailLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			if ($logic->equalsResult('confirm', true)) {
				return $this->redirectPath('/account/user');
			}
			return $this->redirectPath('/account/user/email');
		}

		return $this->view('user_email', $logic->getViewData());
	}

	public function user_plugin_register_get(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserPluginLogic::class, $request, true);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_plugin', $logic->getViewData());
	}

	public function user_plugin_register_post(ActionRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserPluginLogic::class, $request, true);
		if ($logic->run(LogicCallMode::submit())) {
			if ($logic->tryGetResult('plugin_id', $pluginId)) {
				return $this->redirectPath('/account/user/pluginId/' . $pluginId);
			}
			throw new InvalidOperationException();
		}

		return $this->view('user_plugin', $logic->getViewData());
	}
}
