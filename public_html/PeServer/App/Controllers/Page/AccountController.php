<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Account\AccountApiLogic;
use PeServer\App\Models\Domain\Page\Account\AccountLoginLogic;
use PeServer\App\Models\Domain\Page\Account\AccountLogoutLogic;
use PeServer\App\Models\Domain\Page\Account\AccountSignupNotifyLogic;
use PeServer\App\Models\Domain\Page\Account\AccountSignupStep1Logic;
use PeServer\App\Models\Domain\Page\Account\AccountSignupStep2Logic;
use PeServer\App\Models\Domain\Page\Account\AccountUserApiLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserAuditLogLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserEditLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserEmailLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserPasswordLogic;
use PeServer\App\Models\Domain\Page\Account\AccountUserPluginLogic;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Throws\InvalidOperationException;

final class AccountController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->user();
		} else {
			return $this->login_get();
		}
	}

	public function login_get(): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->redirectPath('account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('login', $logic->getViewData());
	}

	public function login_post(): IActionResult
	{
		if ($this->isLoggedIn()) {
			return $this->redirectPath('account/user');
		}

		$logic = $this->createLogic(AccountLoginLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			if ($this->stores->session->tryGet(SessionKey::ACCOUNT, $account)) {
				/** @var SessionAccount $account */
				if ($account->level === UserLevel::SETUP) {
					return $this->redirectPath('management/setup');
				}
			}

			return $this->redirectPath('/account');
		}

		return $this->view('login', $logic->getViewData());
	}

	public function logout(): IActionResult
	{
		$logic = $this->createLogic(AccountLogoutLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->redirectPath('/');
	}

	public function signup_step1_get(): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep1Logic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('signup_step1', $logic->getViewData());
	}

	public function signup_step1_post(): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep1Logic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath("account/signup/notify");
		}

		return $this->view('signup_step1', $logic->getViewData());
	}

	public function signup_notify(): IActionResult
	{
		$logic = $this->createLogic(AccountSignupNotifyLogic::class);
		$logic->run(LogicCallMode::Initialize);
		return $this->view('signup_notify', $logic->getViewData());
	}


	public function signup_step2_get(): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep2Logic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('signup_step2', $logic->getViewData());
	}

	public function signup_step2_post(): IActionResult
	{
		$logic = $this->createLogic(AccountSignupStep2Logic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath("/");
		}

		return $this->view('signup_step2', $logic->getViewData());
	}


	public function user(): IActionResult
	{
		$logic = $this->createLogic(AccountUserLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user', $logic->getViewData());
	}

	public function user_edit_get(): IActionResult
	{
		$logic = $this->createLogic(AccountUserEditLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_edit', $logic->getViewData());
	}

	public function user_edit_post(): IActionResult
	{
		$logic = $this->createLogic(AccountUserEditLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('account/user');
		}

		return $this->view('user_edit', $logic->getViewData());
	}

	public function user_api_get(): IActionResult
	{
		$logic = $this->createLogic(AccountUserApiLogic::class/*, ['$isRegister' => null]*/);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_api', $logic->getViewData());
	}

	public function user_api_post(): IActionResult
	{
		$logic = $this->createLogic(AccountUserApiLogic::class/*, ['$isRegister' => true]*/);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('account/user/api');
		}

		return $this->view('user_api', $logic->getViewData());
	}

	public function user_password_get(): IActionResult
	{
		$logic = $this->createLogic(AccountUserPasswordLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_password', $logic->getViewData());
	}

	public function user_password_post(): IActionResult
	{
		$logic = $this->createLogic(AccountUserPasswordLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			return $this->redirectPath('account/user');
		}

		return $this->view('user_password', $logic->getViewData());
	}

	public function user_email_get(): IActionResult
	{
		$logic = $this->createLogic(AccountUserEmailLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_email', $logic->getViewData());
	}

	public function user_email_post(): IActionResult
	{
		$logic = $this->createLogic(AccountUserEmailLogic::class);
		if ($logic->run(LogicCallMode::Submit)) {
			if ($logic->equalsResult('confirm', true)) {
				return $this->redirectPath('account/user');
			}
			return $this->redirectPath('account/user/email');
		}

		return $this->view('user_email', $logic->getViewData());
	}

	private function user_plugin_get_core(bool $isRegister): IActionResult
	{
		$logic = $this->createLogic(AccountUserPluginLogic::class, ['$isRegister' => $isRegister]);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_plugin', $logic->getViewData());
	}

	private function user_plugin_post_core(bool $isRegister): IActionResult
	{
		$logic = $this->createLogic(AccountUserPluginLogic::class, ['$isRegister' => $isRegister]);
		if ($logic->run(LogicCallMode::Submit)) {
			if ($logic->tryGetResult('plugin_id', $pluginId)) {
				return $this->redirectPath("account/user/plugin/$pluginId");
			}
			throw new InvalidOperationException();
		}

		return $this->view('user_plugin', $logic->getViewData());
	}

	public function user_plugin_register_get(): IActionResult
	{
		return $this->user_plugin_get_core(true);
	}

	public function user_plugin_register_post(): IActionResult
	{
		return $this->user_plugin_post_core(true);
	}

	public function user_plugin_update_get(): IActionResult
	{
		return $this->user_plugin_get_core(false);
	}

	public function user_plugin_update_post(): IActionResult
	{
		return $this->user_plugin_post_core(false);
	}

	public function user_audit_logs(): IActionResult
	{
		$logic = $this->createLogic(AccountUserAuditLogLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_audit_logs', $logic->getViewData());
	}
}
