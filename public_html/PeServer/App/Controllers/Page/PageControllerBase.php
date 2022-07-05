<?php

namespace PeServer\App\Controllers\Page;

use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\App\Controllers\DomainControllerBase;
use PeServer\App\Models\Domain\Page\PageLogicBase;


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
		return $this->session->tryGet(SessionManager::ACCOUNT, $unused);
	}

	protected function viewWithController(string $controllerName, string $action, TemplateParameter $parameter): ViewActionResult
	{
		$parameter->values[PageLogicBase::TEMP_MESSAGES] = $this->temporary->pop(PageLogicBase::TEMP_MESSAGES);

		return parent::viewWithController($controllerName, $action, $parameter);
	}
}
