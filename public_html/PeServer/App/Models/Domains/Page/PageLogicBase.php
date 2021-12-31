<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page;

use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\DomainLogicBase;
use PeServer\App\Models\SessionManager;

abstract class PageLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getUserInfo(): array|null
	{
		if (!SessionManager::hasAccount()) {
			return null;
		}

		$account = SessionManager::getAccount();

		return ['user_id' => $account['user_id']];
	}
}
