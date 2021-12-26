<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\App\Models\SessionKey;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Controllers\DomainControllerBase;

abstract class PageControllerBase extends DomainControllerBase
{
	protected function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	/**
	 * ログイン済みか。
	 *
	 * @return boolean ログイン済み。
	 */
	protected final function isLoggedIn(): bool
	{
		return $this->session->tryGet(SessionKey::ACCOUNT, $_);
	}
}
