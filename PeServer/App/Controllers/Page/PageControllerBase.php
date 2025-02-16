<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Models\SessionKey;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\App\Controllers\DomainControllerBase;
use PeServer\App\Models\Domain\Page\PageLogicBase;

/**
 * [PAGE] ページ基底コントローラ。
 */
abstract class PageControllerBase extends DomainControllerBase
{
	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	/**
	 * ログイン済みか。
	 *
	 * @return boolean ログイン済み。
	 */
	final protected function isLoggedIn(): bool
	{
		return $this->stores->session->tryGet(SessionKey::ACCOUNT, $unused);
	}

	protected function viewWithController(string $controllerName, string $action, TemplateParameter $parameter): ViewActionResult
	{
		$parameter->values[PageLogicBase::TEMP_MESSAGES] = $this->stores->temporary->pop(PageLogicBase::TEMP_MESSAGES);

		return parent::viewWithController($controllerName, $action, $parameter);
	}

	#endregion

	#region DomainControllerBase

	protected function getSkipBaseName(): string
	{
		return __NAMESPACE__;
	}

	#endregion
}
