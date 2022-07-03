<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Account\AccountUserLogic;
use PeServer\App\Models\Domain\Page\Account\AccountLoginLogic;
use PeServer\App\Models\Domain\Page\Account\AccountLogoutLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserEditLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserEmailLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserPluginLogic;
use PeServer\App\Models\Domain\Page\Account\AccountSignupStep1Logic;
use PeServer\App\Models\Domain\Page\Account\AccountSignupStep2Logic;
use PeServer\App\Models\Domain\Page\Account\AccountUserPasswordLogic;

final class AccountController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(HttpRequest $request): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->user($request);
		} else {
			return $this->login_get($request);
		}
	}

	public function login_get(HttpRequest $request): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->redirectPath('account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('login', $logic->getViewData());
	}

	public function login_post(HttpRequest $request): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->redirectPath('account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			if ($this->session->tryGet(SessionManager::ACCOUNT, $account)) {
				if ($account['level'] === UserLevel::SETUP) {
					return $this->redirectPath('setting/setup');
				}
			}

			return $this->redirectPath('/');
		}

		return $this->view('login', $logic->getViewData());
	}

	public function logout(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountLogoutLogic::class, $request);
		$logic->run(LogicCallMode::submit());

		return $this->redirectPath('/');
	}

	public function signup_step1_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep1Logic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('view_signup_step1', $logic->getViewData());
	}

	public function signup_step1_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep1Logic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			if ($logic->tryGetResult('token', $token)) {
				return $this->redirectPath("account/signup/$token");
			}
			throw new InvalidOperationException();
		}

		return $this->view('view_signup_step1', $logic->getViewData());
	}

	public function signup_step2_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep2Logic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('view_signup_step2', $logic->getViewData());
	}

	public function signup_step2_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep2Logic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath("/");
		}

		return $this->view('view_signup_step2', $logic->getViewData());
	}


	public function user(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user', $logic->getViewData());
	}

	public function user_edit_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEditLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_edit', $logic->getViewData());
	}

	public function user_edit_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEditLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('account/user');
		}

		return $this->view('user_edit', $logic->getViewData());
	}

	public function user_password_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserPasswordLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_password', $logic->getViewData());
	}

	public function user_password_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserPasswordLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			return $this->redirectPath('account/user');
		}

		return $this->view('user_password', $logic->getViewData());
	}

	public function user_email_get(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEmailLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_email', $logic->getViewData());
	}

	public function user_email_post(HttpRequest $request): IActionResult
	{
		$logic = $this->createLogic(AccountUserEmailLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			if ($logic->equalsResult('confirm', true)) {
				return $this->redirectPath('account/user');
			}
			return $this->redirectPath('account/user/email');
		}

		return $this->view('user_email', $logic->getViewData());
	}

	private function user_plugin_get_core(HttpRequest $request, bool $isRegister): IActionResult
	{
		$logic = $this->createLogic(AccountUserPluginLogic::class, $request, $isRegister);
		$logic->run(LogicCallMode::initialize());

		return $this->view('user_plugin', $logic->getViewData());
	}

	private function user_plugin_post_core(HttpRequest $request, bool $isRegister): IActionResult
	{
		$logic = $this->createLogic(AccountUserPluginLogic::class, $request, $isRegister);
		if ($logic->run(LogicCallMode::submit())) {
			if ($logic->tryGetResult('plugin_id', $pluginId)) {
				return $this->redirectPath("account/user/plugin/$pluginId");
			}
			throw new InvalidOperationException();
		}

		return $this->view('user_plugin', $logic->getViewData());
	}

	public function user_plugin_register_get(HttpRequest $request): IActionResult
	{
		return $this->user_plugin_get_core($request, true);
	}

	public function user_plugin_register_post(HttpRequest $request): IActionResult
	{
		return $this->user_plugin_post_core($request, true);
	}

	public function user_plugin_update_get(HttpRequest $request): IActionResult
	{
		return $this->user_plugin_get_core($request, false);
	}

	public function user_plugin_update_post(HttpRequest $request): IActionResult
	{
		return $this->user_plugin_post_core($request, false);
	}
}
