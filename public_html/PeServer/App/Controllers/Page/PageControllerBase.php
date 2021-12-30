<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\App\Models\SessionManager;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Controllers\DomainControllerBase;

abstract class PageControllerBase extends DomainControllerBase
{
	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	/**
	 * ログイン済みか。
	 *
	 * @return boolean ログイン済み。
	 */
	protected final function isLoggedIn(): bool
	{
		return $this->session->tryGet(SessionManager::ACCOUNT, $_);
	}
}
