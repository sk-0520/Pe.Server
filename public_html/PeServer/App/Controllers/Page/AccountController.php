<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\ActionOptions;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Models\Domains\Page\Account\AccountLoginLogic;

class AccountController extends PageControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function index(ActionRequest $request, ActionOptions $options): void
	{
		$this->login_get($request, $options);
	}

	public function login_get(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(AccountLoginLogic::class, $request, $options);
		$logic->run(LogicCallMode::initialize());

		$this->view('login', $logic->getViewData());
	}

	public function login_post(ActionRequest $request, ActionOptions $options): void
	{
		$logic = $this->createLogic(AccountLoginLogic::class, $request, $options);
		if ($logic->run(LogicCallMode::submit())) {
			$this->redirectPath('/');
		}

		$this->view('login', $logic->getViewData());
	}
}
