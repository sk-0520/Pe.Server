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
		$this->login($request);
	}

	public function login(ActionRequest $request): void
	{
		$logic = $this->createLogic(AccountLoginLogic::class, $request);
		$logic->run(LogicCallMode::initialize());

		$this->view('login', $logic->getViewData());
	}
}
