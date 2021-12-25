<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page;

use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\DomainLogicBase;

abstract class PageLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getAuditUserInfo(): array|null
	{
		$user = $this->getSession('user', null);
		if (is_null($user)) {
			return null;
		}

		return ['userId' => $user['id']];
	}
}