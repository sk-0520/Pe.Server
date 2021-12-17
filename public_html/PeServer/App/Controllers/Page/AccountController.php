<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Models\Domains\Page\Account\AccountLoginLogic;

class AccountController extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function index(ActionRequest $request): void
	{
		$this->login_get($request);
	}

	public function login_get(ActionRequest $request): void
	{
		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		$this->view('login', $logic->getViewData());
	}

	public function login_post(ActionRequest $request): void
	{
		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		if ($logic->run(LogicCallMode::submit())) {
			$this->redirectPath('/');
		}

		$this->view('login', $logic->getViewData());
	}
}
