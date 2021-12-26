<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page;

use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\DomainLogicBase;
use PeServer\App\Models\SessionKey;

abstract class PageLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getUserInfo(): array|null
	{
		$user = $this->getSession(SessionKey::ACCOUNT, null);
		if (is_null($user)) {
			return null;
		}

		return ['user_id' => $user['id']];
	}
}
